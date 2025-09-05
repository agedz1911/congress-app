<?php

namespace App\Filament\Resources\Accommodation\BookingResource\Pages;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Accommodation\BookingResource;
use App\Filament\Resources\Accommodation\BookingResource\Schemas\BookPrice;
use App\Models\Accommodation\Booking;
use App\Models\Accommodation\BookingTransaction;
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
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Accommodation extends Page
{
    use InteractsWithForms;

    protected static string $resource = BookingResource::class;
    public ?array $data = [];

    public function mount()
    {
        $this->form->fill([
            'booking_code' => 'ACC-' . random_int(10000, 99999),
            'discount' => 0,
            'total' => 0,
            'amount' => 0,
            'status' => 'new',
        ]);
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
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Booking')
                        ->icon('heroicon-m-building-office')
                        ->schema([
                            Section::make()
                                ->schema([
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
                                        ->reactive() // agar total bisa dihitung ulang ketika participant berubah
                                        ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                            // Recalculate total jika participant berubah
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
                                        ->unique(Booking::class, 'booking_code', ignoreRecord: true),
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
                        Submit Booking
                    </x-filament::button>
                    BLADE)
                ))
            ])
            ->statePath('data');
    }


    public function create()
    {
        $data = $this->form->getState();
        $this->calculateGrandTotal();
        try {
            // Validasi dasar tanggal
            if (empty($data['check_in_date']) || empty($data['check_out_date'])) {
                Notification::make()
                    ->title('Tanggal tidak lengkap')
                    ->body('Check-in dan Check-out harus diisi.')
                    ->danger()
                    ->send();
                return;
            }

            $checkIn = \Carbon\Carbon::parse($data['check_in_date']);
            $checkOut = \Carbon\Carbon::parse($data['check_out_date']);

            // check_out_date tidak boleh kurang dari check_in_date
            if ($checkOut->lt($checkIn)) {
                Notification::make()
                    ->title('Tanggal tidak valid')
                    ->body('Check-out tidak boleh lebih awal dari check-in.')
                    ->danger()
                    ->send();
                return;
            }

            // Jika Anda ingin total_night dihitung otomatis:
            if (empty($data['total_night'])) {
                // Menggunakan selisih hari. Jika harus minimal 1 malam, pastikan > 0.
                $nights = $checkIn->diffInDays($checkOut);
                if ($nights <= 0) {
                    Notification::make()
                        ->title('Durasi menginap tidak valid')
                        ->body('Minimal 1 malam.')
                        ->danger()
                        ->send();
                    return;
                }
                $data['total_night'] = $nights;
            }

            // Validasi entity
            $room = HotelRoom::query()->whereKey($data['room_id'] ?? null)->lockForUpdate()->first();
            $hotel = Hotel::find($data['hotel_id'] ?? null);
            $participant = Participant::find($data['participant_id'] ?? null);

            if (!$room || !$hotel || !$participant) {
                Notification::make()
                    ->title('Data tidak valid')
                    ->body('Hotel/Room/Participant tidak ditemukan.')
                    ->danger()
                    ->send();
                return;
            }

            if ((int)$room->hotel_id !== (int)$hotel->id) {
                Notification::make()
                    ->title('Relasi tidak valid')
                    ->body('Room tidak termasuk ke hotel yang dipilih.')
                    ->danger()
                    ->send();
                return;
            }

            DB::beginTransaction();

            // Cek kuota room
            $available = max(0, (int)$room->quota - (int)$room->used_count);
            if ($available <= 0) {
                DB::rollBack();
                Notification::make()
                    ->title('Kuota kamar habis')
                    ->danger()
                    ->send();
                return;
            }

            // Hitung subtotal berdasarkan negara participant
            $unitPrice = BookPrice::getRoomUnitBasePrice($room, $participant);
            $subtotal = max(0, $unitPrice * (int)$data['total_night']);

            // Validasi kupon saat submit (lock for update)
            $couponCode = $data['coupon'] ?? null;
            $validatedDiscount = 0.0;
            $coupon = null;

            if ($couponCode) {
                $coupon = Coupon::where('name', $couponCode)->lockForUpdate()->first();
                if ($coupon && $coupon->isCurrentlyActive() && $coupon->isQuotaAvailable()) {
                    $validatedDiscount = (float) $coupon->computeDiscountForSubtotal($subtotal);
                } else {
                    // Abaikan kupon jika tidak valid
                    $couponCode = null;
                    $validatedDiscount = 0.0;

                    Notification::make()
                        ->title('Kupon diabaikan')
                        ->body('Kupon tidak aktif/masa berlaku habis/kuota habis.')
                        ->warning()
                        ->send();
                }
            }

            // Pastikan discount <= subtotal
            $discount = min(max(0, $validatedDiscount), $subtotal);
            $total = max(0, $subtotal - $discount);

            // Simpan booking
            $booking = Booking::create([
                'booking_code'   => $data['booking_code'],
                'hotel_id'       => $hotel->id,
                'room_id'        => $room->id,
                'participant_id' => $participant->id,
                'check_in_date'  => $checkIn,
                'check_out_date' => $checkOut,
                'total_night'    => (int)$data['total_night'],
                'coupon'         => $couponCode,
                'discount'       => (int)$discount,
                'subtotal'       => (int)$subtotal,
                'total'          => (int)$total,
                'status'         => $data['status'] ?? 'new',
            ]);

            // Kurangi kuota room (increment used_count)
            $room->increment('used_count', 1);

            // Jika kupon valid, increment used_count kupon
            if ($coupon && $couponCode) {
                $coupon->increment('used_count', 1);
            }

            if (isset($data['payment_method'], $data['payment_status'])) {
                $participant = Participant::find($data['participant_id']);
                $kursValue = 1;

                if ($participant) {
                    $currency = strtolower($participant->country) === 'indonesia'
                        ? Currency::where('region', 'indonesia')->first()
                        : Currency::where('region', 'united states')->first();

                    if ($currency) $kursValue = $currency->kurs;
                }

                $calculatedKurs = $total * $kursValue;

                BookingTransaction::create([
                    'booking_id'        => $booking->id,
                    'payment_method'  => $data['payment_method'],
                    'payment_date'    => $data['payment_date'],
                    'payment_status'  => $data['payment_status'],
                    'amount'          => $this->data['amount'],
                    'attachment'      => $data['attachment'] ? (is_array($data['attachment']) ? $data['attachment'][0] : $data['attachment']) : null,
                    'kurs'            => $calculatedKurs,
                ]);
            }


            DB::commit();

            Notification::make()
                ->title('Booking berhasil')
                ->success()
                ->send();
            $this->redirect(BookingResource::getUrl('index'));
        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Gagal melakukan booking')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }


    protected static string $view = 'filament.resources.accommodation.booking-resource.pages.accommodation';
}
