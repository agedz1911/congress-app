<?php

namespace App\Filament\Resources\Accommodation\BookingResource\Pages;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Accommodation\BookingResource;
use App\Filament\Resources\Accommodation\BookingResource\Schemas\BookPrice;
use App\Models\Accommodation\Booking;
use App\Models\Accommodation\Hotel;
use App\Models\Accommodation\HotelRoom;
use App\Models\Currency;
use App\Models\Manage\Coupon;
use App\Models\Registration\Participant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class AccommodationEdit extends Page
{
    use InteractsWithForms;
    protected static string $resource = BookingResource::class;

    public ?array $data = [];

    public Booking $record;

    public function mount()
    {
        $this->record->load(['bookingTransaction']);

        $data = $this->record->attributesToArray();

        if ($this->record->bookingTransaction) {
            $data['amount'] = $this->record->bookingTransaction->amount;
            $data['payment_method'] = $this->record->bookingTransaction->payment_method;
            $data['payment_status'] = $this->record->bookingTransaction->payment_status;
            $data['payment_date'] = $this->record->bookingTransaction->payment_date;
            $data['attachment'] = $this->record->bookingTransaction->attachment;
        }

        $this->form->fill($data);
    }

    protected function calculateGrandTotal()
    {
        $roomId = $this->data['room_id'] ?? null;
        $participantId = $this->data['participant_id'] ?? null;
        $totalNight = (int) ($this->data['total_night'] ?? 0);
        $subtotal = 0;

        if ($roomId && $participantId && $totalNight > 0) {
            $room = HotelRoom::find($roomId);
            $participant = Participant::find($participantId);

            if ($room && $participant) {
                $unitPrice = BookPrice::getRoomUnitBasePrice($room, $participant);
                $subtotal = max(0, $unitPrice * $totalNight);
            }
        }

        $this->data['subtotal'] = $subtotal;

        $discount = $this->data['discount'] ?? 0;
        $discount = min(max(0, $discount), $subtotal);

        $this->data['total'] = max(0, $subtotal - $discount);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Step::make('Booking')
                    ->icon('heroicon-m-building-office')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('id')
                                    ->hidden()
                                    ->dehydrated(),
                                Select::make('participant_id')
                                    ->label('Participant')
                                    ->options(
                                        Participant::query()
                                            ->get()
                                            ->mapWithKeys(fn($user) => [
                                                $user->id => $user->id_participant . ' | ' . $user->first_name . ' ' . $user->last_name
                                            ])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        $livewire->calculateGrandTotal();
                                    })
                                    ->columnSpanFull(),
                                Select::make('hotel_id')
                                    ->label('Hotel')
                                    ->searchable()
                                    ->native(false)
                                    ->required()
                                    ->reactive()
                                    ->options(Hotel::query()->pluck('name', 'id'))
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        // Reset room saat hotel berubah
                                        $set('room_id', null);
                                    }),
                                Select::make('room_id')
                                    ->label('Room')
                                    ->required()
                                    ->reactive()
                                    ->native(false)
                                    ->options(function (callable $get) {
                                        $hotelId = $get('hotel_id');
                                        if (!$hotelId) {
                                            return HotelRoom::query()
                                                ->get()
                                                ->mapWithKeys(fn($room) => [
                                                    $room->id => $room->hotel->name . ' | ' . $room->room_type
                                                ]);
                                        }
                                        return HotelRoom::query()
                                            ->get()
                                            ->where('hotel_id', $hotelId)
                                            ->mapWithKeys(fn($room) => [
                                                $room->id => $room->hotel->name . ' | ' . $room->price_idr . ' | ' . $room->price_usd
                                            ]);
                                    })
                                    ->afterStateUpdated(function ($livewire) {
                                        // Recalculate ketika room berubah
                                        $livewire->calculateGrandTotal();
                                    }),
                                DatePicker::make('check_in_date')
                                    ->required()
                                    ->label('Check-in')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        // Hitung total_night otomatis saat check-in berubah
                                        $checkIn = $state;
                                        $checkOut = $get('check_out_date');

                                        if ($checkIn && $checkOut) {
                                            try {
                                                $in = \Carbon\Carbon::parse($checkIn);
                                                $out = \Carbon\Carbon::parse($checkOut);

                                                // Pastikan check_out >= check_in, jika tidak reset
                                                if ($out->lt($in)) {
                                                    $set('total_night', null);
                                                } else {
                                                    $nights = $in->diffInDays($out);
                                                    $set('total_night', $nights > 0 ? $nights : null);
                                                }
                                            } catch (\Throwable $e) {
                                                $set('total_night', null);
                                            }
                                        } else {
                                            $set('total_night', null);
                                        }

                                        // Recalculate total
                                        $livewire->calculateGrandTotal();
                                    }),

                                DatePicker::make('check_out_date')
                                    ->required()
                                    ->label('Check-out')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                        // Hitung total_night otomatis saat check-out berubah
                                        $checkOut = $state;
                                        $checkIn = $get('check_in_date');

                                        if ($checkIn && $checkOut) {
                                            try {
                                                $in = \Carbon\Carbon::parse($checkIn);
                                                $out = \Carbon\Carbon::parse($checkOut);

                                                // Pastikan check_out >= check_in, jika tidak reset
                                                if ($out->lt($in)) {
                                                    $set('total_night', null);
                                                } else {
                                                    $nights = $in->diffInDays($out);
                                                    $set('total_night', $nights > 0 ? $nights : null);
                                                }
                                            } catch (\Throwable $e) {
                                                $set('total_night', null);
                                            }
                                        } else {
                                            $set('total_night', null);
                                        }

                                        // Recalculate total
                                        $livewire->calculateGrandTotal();
                                    }),

                                TextInput::make('total_night')
                                    ->label('Total Night')
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive(),
                                TextInput::make('booking_code')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(20)
                                    ->disabled(),
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive()
                                    ->columnSpanFull(),
                            ])->columns(2)
                    ]),
                Step::make('Summary')
                    ->icon('heroicon-m-shopping-cart')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('coupon')
                                    ->label('Coupon Code')
                                    ->reactive()
                                    ->debounce(600)
                                    ->afterStateUpdated(function (callable $set, callable $get, $livewire, ?string $state) {
                                        // Reset discount awal
                                        $set('discount', 0);

                                        // Hitung subtotal terkini untuk evaluasi kupon
                                        $roomId = $get('room_id');
                                        $participantId = $get('participant_id');
                                        $totalNight = (int) ($get('total_night') ?? 0);
                                        $subtotal = 0;

                                        if ($roomId && $participantId && $totalNight > 0) {
                                            $room = HotelRoom::find($roomId);
                                            $participant = Participant::find($participantId);
                                            if ($room && $participant) {
                                                $unit = BookPrice::getRoomUnitBasePrice($room, $participant);
                                                $subtotal = max(0, $unit * $totalNight);
                                            }
                                        }

                                        if (blank($state)) {
                                            Notification::make()
                                                ->title('Coupon cleared')
                                                ->body('Kupon dihapus. Diskon direset ke 0.')
                                                ->info()
                                                ->duration(2000)
                                                ->send();
                                            $livewire->calculateGrandTotal();
                                            return;
                                        }

                                        $coupon = Coupon::where('name', $state)->first();
                                        if (!$coupon) {
                                            Notification::make()
                                                ->title('Coupon tidak ditemukan')
                                                ->body('Kode kupon tidak valid.')
                                                ->danger()
                                                ->send();
                                            $livewire->calculateGrandTotal();
                                            return;
                                        }

                                        if (!$coupon->isCurrentlyActive()) {
                                            Notification::make()
                                                ->title('Coupon tidak aktif')
                                                ->body('Kupon belum aktif, sudah berakhir, atau dinonaktifkan.')
                                                ->warning()
                                                ->send();
                                            $livewire->calculateGrandTotal();
                                            return;
                                        }

                                        if (!$coupon->isQuotaAvailable()) {
                                            Notification::make()
                                                ->title('Kuota kupon habis')
                                                ->body('Kuota penggunaan kupon ini telah habis.')
                                                ->danger()
                                                ->send();
                                            $livewire->calculateGrandTotal();
                                            return;
                                        }

                                        $discount = (float) $coupon->computeDiscountForSubtotal($subtotal);
                                        $set('discount', $discount);

                                        $quotaInfo = is_null($coupon->remainingQuota())
                                            ? 'Kuota: unlimited'
                                            : 'Sisa kuota: ' . $coupon->remainingQuota();

                                        $label = $coupon->type === 'percent'
                                            ? 'Diskon ' . rtrim(rtrim(number_format($coupon->nominal, 2), '0'), '.') . '% diterapkan.'
                                            : 'Diskon Rp ' . number_format($coupon->nominal, 0, ',', '.') . ' diterapkan.';

                                        Notification::make()
                                            ->title('Coupon diterapkan')
                                            ->body($label . ' ' . $quotaInfo)
                                            ->success()
                                            ->send();

                                        $livewire->calculateGrandTotal();
                                    }),

                                TextInput::make('discount')
                                    ->label('Discount Amount')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive()
                                    ->afterStateUpdated(function ($livewire) {
                                        $livewire->calculateGrandTotal();
                                    }),

                                TextInput::make('total')
                                    ->label('Total Amount')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive(),

                                ToggleButtons::make('status')
                                    ->options(BookingStatus::class)
                                    ->inline(true)
                                    ->required(),
                            ])
                    ]),
                Step::make('Payment')
                    ->icon('heroicon-m-credit-card')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('amount')
                                    ->numeric()
                                    ->dehydrated(),
                                ToggleButtons::make('payment_method')
                                    ->inline(true)
                                    ->options(PaymentMethod::class)
                                    ->required(),
                                ToggleButtons::make('payment_status')
                                    ->inline(true)
                                    ->options(PaymentStatus::class)
                                    ->required(),
                                DatePicker::make('payment_date')
                                    ->label('Payment Date')
                                    ->native(false),
                            ])->columns(2),
                        FileUpload::make('attachment')
                            ->maxSize(3072)
                            ->downloadable()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->image()
                            ->imageEditor()
                            ->storeFileNamesIn('attachment_file_names')
                            ->directory('Payment_Receipt'),
                    ]),
            ])->submitAction(new HtmlString(
                Blade::render(<<<BLADE
                    <x-filament::button type="submit" size="sm">
                        Save Booking
                    </x-filament::button>
                    BLADE)
            ))
        ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save()
    {

        try {
            $this->calculateGrandTotal();

            $data = $this->form->getState();

            $transactionData = [
                'amount' => $this->data['amount'] ?? $data['amount'],
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => $data['payment_status'] ?? null,
                'payment_date' => $data['payment_date'] ?? null,
                'attachment' => $data['attachment'] ?? null,
            ];

            $participant = Participant::find($data['participant_id']);
            $total = $this->data['total'] ?? 0;
            $kursValue = 1;

            if ($participant) {
                if (strtolower($participant->country) === 'indonesia') {
                    $currency = Currency::where('region', 'indonesia')->first();
                } else {
                    $currency = Currency::where('region', 'united states')->first();
                }
                if ($currency) {
                    $kursValue = $currency->kurs;
                }
            }

            $calculatedKurs = $total * $kursValue;
            $transactionData['kurs'] = $calculatedKurs;

            unset($data['amount'], $data['payment_method'], $data['payment_status'], $data['payment_date'], $data['attachment']);

            // $data['total'] = $this->data['total'];

            // Update registration order
            $this->record->update($data);

            if ($this->record->bookingTransaction) {
                $this->record->bookingTransaction->update($transactionData);
            } else {
                $this->record->bookingTransaction()->create(array_merge($transactionData, [
                    'booking_id' => $this->record->id
                ]));
            }

            DB::commit();

            Notification::make()
                ->title('Booking berhasil diperbarui')
                ->success()
                ->send();

            $this->redirect(BookingResource::getUrl('index'));
        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Gagal memperbarui booking')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }


    protected static string $view = 'filament.resources.accommodation.booking-resource.pages.accommodation-edit';
}
