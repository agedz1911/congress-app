<?php

namespace App\Filament\Resources\Registration\OrderResource\Pages;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RegStatus;
use App\Filament\Resources\Registration\OrderResource;
use App\Filament\Resources\Registration\OrderResource\Schemas\Pricing;
use App\Models\Currency;
use App\Models\Manage\Coupon;
use App\Models\Registration\Order;
use App\Models\Registration\OrderItem;
use App\Models\Registration\Participant;
use App\Models\Registration\Product;
use App\Models\Registration\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Registration extends Page
{
    use InteractsWithForms;
    protected static string $resource = OrderResource::class;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'regItems' => [
                [
                    'quantity' => 1,
                    'unit_price' => 0,
                ]
            ],
            'reg_code' => 'REG-' . random_int(10000, 99999),
            'discount' => 0,
            'total' => 0,
            'amount' => 0,
            'status' => 'new',
            // 'payment_date' => now()->format('Y-m-d'),
        ]);
    }

    protected function calculateGrandTotal(): void
    {
        $regItems = $this->data['regItems'] ?? [];
        $subtotal = 0.0;

        foreach ($regItems as $item) {
            $subtotal += (float) ($item['unit_price'] ?? 0);
        }

        $discount = (float) ($this->data['discount'] ?? 0);
        $discount = min(max(0, $discount), $subtotal);

        $this->data['total'] = max(0, $subtotal - $discount);
        // $this->data['amount'] = $grandTotal;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Registration Detail')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('reg_code')
                                        ->label('Registration Code')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->maxLength(20)
                                        ->unique(Order::class, 'reg_code', ignoreRecord: true),
                                    Select::make('participant_id')
                                        ->label('Participant')
                                        ->options(Participant::all()->mapWithKeys(function ($user) {
                                            return [$user->id => $user->id_participant . ' | ' . $user->first_name . ' ' . $user->last_name];
                                        }))
                                        ->searchable()
                                        ->required(),

                                ])->columns(2)
                        ]),
                    Step::make('Product Registration')
                        ->schema([
                            Repeater::make('regItems')
                                ->schema([
                                    Section::make()
                                        ->schema([
                                            Select::make('product_id')
                                                ->label('Product')
                                                ->options(fn() => Product::query()
                                                    ->where('is_active', true)
                                                    ->orderBy('name')
                                                    ->pluck('name', 'id'))
                                                ->reactive()
                                                ->searchable()
                                                ->required()
                                                ->afterStateUpdated(function (callable $set, callable $get, $livewire) {
                                                    $productId = $get('product_id');
                                                    $quantity = $get('quantity') ?? 1;
                                                    $participantId = $livewire->data['participant_id'] ?? null;
                                                    $participant = $participantId ? Participant::find($participantId) : null;

                                                    if ($productId && $participant) {
                                                        $product = Product::find($productId);
                                                        if ($product) {
                                                            $unit_price = Pricing::calcLineUnitPrice($product, $participant, $quantity);
                                                            $set('unit_price', $unit_price);
                                                            $livewire->calculateGrandTotal();
                                                        }
                                                    }
                                                }),
                                            TextInput::make('quantity')
                                                ->label('Quantity')
                                                ->numeric()
                                                ->reactive()
                                                ->required()
                                                ->afterStateUpdated(function (callable $set, callable $get, $livewire) {
                                                    $productId = $get('product_id');
                                                    $quantity = $get('quantity') ?? 1;
                                                    $participantId = $livewire->data['participant_id'] ?? null;
                                                    $participant = $participantId ? Participant::find($participantId) : null;

                                                    if ($productId && $quantity > 0 && $participant) {
                                                        $product = Product::find($productId);
                                                        if ($product) {
                                                            $unit_price = Pricing::calcLineUnitPrice($product, $participant, $quantity);
                                                            $set('unit_price', $unit_price);
                                                            $livewire->calculateGrandTotal();
                                                        }
                                                    }
                                                }),
                                            TextInput::make('unit_price')
                                                ->label('Unit Price')
                                                ->numeric()
                                                ->disabled()
                                                ->dehydrated()
                                                ->reactive(),
                                        ])->columns(3),
                                ])
                                ->addActionLabel('Add Product')
                                ->reorderable(false)
                                ->afterStateUpdated(function ($livewire) {
                                    $livewire->calculateGrandTotal();
                                }),

                        ]),
                    Step::make('Summary')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('coupon')
                                        ->label('Coupon Code')
                                        ->reactive()
                                        ->debounce(600)
                                        ->afterStateUpdated(function (callable $set, callable $get, $livewire, ?string $state) {
                                            $set('discount', 0);

                                            // Hitung subtotal saat ini
                                            $regItems = $get('regItems') ?? [];
                                            $subtotal = 0.0;
                                            foreach ($regItems as $item) {
                                                $subtotal += (float) ($item['unit_price'] ?? 0);
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

                                            $discount = $coupon->computeDiscountForSubtotal($subtotal);
                                            $set('discount', $discount);

                                            $quotaInfo = is_null($coupon->remainingQuota())
                                                ? 'Kuota: unlimited'
                                                : 'Sisa kuota: ' . $coupon->remainingQuota();

                                            $label = $coupon->type === 'percent'
                                                ? 'Diskon ' . rtrim(rtrim(number_format($coupon->nominal, 2), '0'), '.') . '% diterapkan.'
                                                : 'Diskon Rp ' . number_format($coupon->nominal, 2) . ' diterapkan.';

                                            Notification::make()
                                                ->title('Coupon diterapkan')
                                                ->body($label . ' ' . $quotaInfo)
                                                ->success()
                                                ->send();

                                            $livewire->calculateGrandTotal();
                                        }),
                                    TextInput::make('discount')
                                        ->label('Discount Amount')
                                        ->numeric()
                                        ->disabled()     // agar mengikuti kupon
                                        ->dehydrated()
                                        ->reactive()
                                        ->afterStateUpdated(function ($livewire) {
                                            $livewire->calculateGrandTotal();
                                        }),
                                    TextInput::make('total')
                                        ->label('Total Amount')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(),
                                    ToggleButtons::make('status')
                                        ->required()
                                        ->options(RegStatus::class)
                                        ->inline(true),
                                ])
                        ]),

                    Step::make('Payment')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('amount')
                                        ->label('Payment Amount')
                                        ->numeric()
                                        // ->disabled()
                                        ->dehydrated(),
                                    ToggleButtons::make('payment_method')
                                        ->label('Payment Method')
                                        ->options(PaymentMethod::class)
                                        ->inline()
                                        ->required(),
                                    ToggleButtons::make('payment_status')
                                        ->label('Payment Status')
                                        ->options(PaymentStatus::class)
                                        ->inline()
                                        ->required(),
                                    DatePicker::make('payment_date')
                                        ->label('Payment Date'),
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
                        ])

                ])->submitAction(new HtmlString(
                    Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="sm"
                        >
                            Submit Registration
                        </x-filament::button>
                    BLADE)
                ))
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $this->calculateGrandTotal();

        try {
            DB::beginTransaction();

            $couponCode = $data['coupon'] ?? null;
            $validatedDiscount = 0.0;

            // Hitung subtotal dari items
            $regItems = $data['regItems'] ?? [];
            $subtotal = 0.0;
            foreach ($regItems as $item) {
                $subtotal += (float) ($item['unit_price'] ?? 0);
            }

            $coupon = null;
            if ($couponCode) {
                // lock row untuk menghindari race pada kuota
                $coupon = Coupon::where('name', $couponCode)->lockForUpdate()->first();

                if ($coupon && $coupon->isCurrentlyActive() && $coupon->isQuotaAvailable()) {
                    $validatedDiscount = $coupon->computeDiscountForSubtotal($subtotal);
                } else {
                    $couponCode = null;
                    $validatedDiscount = 0.0;

                    Notification::make()
                        ->title('Coupon tidak valid saat submit')
                        ->body('Kupon tidak aktif/masa berlaku habis/kuota habis. Diskon diabaikan.')
                        ->warning()
                        ->send();
                }
            }

            $discount = min($validatedDiscount, $subtotal);
            $total = max(0, $subtotal - $discount);

            // Create Order
            $registrationOrder = Order::create([
                'reg_code'       => $data['reg_code'],
                'participant_id' => $data['participant_id'],
                'total'          => $total,
                'discount'       => $discount,
                'coupon'         => $couponCode,
                'status'         => $data['status'],
            ]);

            // Items
            if (is_array($regItems)) {
                foreach ($regItems as $item) {
                    if (isset($item['product_id'], $item['quantity'], $item['unit_price'])) {
                        OrderItem::create([
                            'product_id' => $item['product_id'],
                            'order_id'   => $registrationOrder->id,
                            'quantity'   => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                        ]);
                    }
                }
            }

            // Increment kuota jika kupon dipakai
            if ($coupon && $couponCode) {
                $coupon->increment('used_count', 1);
            }

            // Transaction (kurs) seperti sebelumnya
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

                Transaction::create([
                    'order_id'        => $registrationOrder->id,
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
                ->title('Registration created successfully!')
                ->success()
                ->send();

            $this->redirect(OrderResource::getUrl('index'));
        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Failed to create registration')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected static string $view = 'filament.resources.registration.order-resource.pages.registration';
}
