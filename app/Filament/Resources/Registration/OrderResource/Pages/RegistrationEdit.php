<?php

namespace App\Filament\Resources\Registration\OrderResource\Pages;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RegStatus;
use App\Filament\Resources\Registration\OrderResource;
use App\Filament\Resources\Registration\OrderResource\Schemas\Pricing;
use App\Models\Currency;
use App\Models\Registration\Order;
use App\Models\Registration\Participant;
use App\Models\Registration\Product;
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
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class RegistrationEdit extends Page
{
    use InteractsWithForms;
    protected static string $resource = OrderResource::class;

    public ?array $data = [];

    public Order $record;

    public function mount(): void
    {
        // Load the record with transaction relationship
        $this->record->load(['transaction', 'items.product']);

        // Prepare data including transaction data
        $data = $this->record->attributesToArray();

        // Add transaction data if exists
        if ($this->record->transaction) {
            $data['amount'] = $this->record->transaction->amount;
            $data['payment_method'] = $this->record->transaction->payment_method;
            $data['payment_status'] = $this->record->transaction->payment_status;
            $data['payment_date'] = $this->record->transaction->payment_date;
            $data['attachment'] = $this->record->transaction->attachment;
        }

        // Add regItems data
        $data['items'] = $this->record->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        })->toArray();

        $this->form->fill($data);
    }

    protected function calculateGrandTotal(): void
    {
        $items = $this->data['items'] ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['unit_price'] ?? 0;
        }

        $discount = $this->data['discount'] ?? 0;
        $grandTotal = max(0, $subtotal - $discount);

        $this->data['total'] = $grandTotal;
        $this->data['amount'] = $grandTotal;
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
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
                                        ->maxLength(20),
                                    Select::make('participant_id')
                                        ->label('Participant')
                                        ->options(Participant::all()->mapWithKeys(function ($user) {
                                            return [$user->id => $user->id_participant . ' | ' . $user->name . ' ' . $user->last_name];
                                        }))
                                        ->searchable()
                                        ->required(),
                                ])->columns(2)
                        ]),
                    Step::make('Product Registration')
                        ->schema([
                            Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Section::make()
                                        ->schema([
                                            Select::make('product_id')
                                                ->label('Product')
                                                ->options(Product::all()->mapWithKeys(function ($product) {
                                                    return [$product->id => $product->name];
                                                }))
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
                                                            $unit_price = Pricing::calcLineUnitPrice($product, $participant, $quantity);;
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
                            TextInput::make('coupon')
                                ->label('Coupon Code'),
                            TextInput::make('discount')
                                ->label('Discount Amount')
                                ->numeric()
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
                        ]),

                    Step::make('Payment')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('amount')
                                        ->label('Payment Amount')
                                        ->numeric()
                                        ->required()
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
                            Save
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
            DB::beginTransaction();

            // Ensure we have the calculated total
            $this->calculateGrandTotal();

            $data = $this->form->getState();

            // Separate transaction data from registration data
            $transactionData = [
                'amount' => $this->data['amount'] ?? $data['amount'],
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => $data['payment_status'] ?? null,
                'payment_date' => $data['payment_date'] ?? null,
                'attachment' => $data['attachment'] ?? null,
            ];

            // Calculate kurs based on participant country and currency table
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

            // Separate regItems data
            $regItemsData = $data['items'] ?? [];

            // Remove transaction and regItems fields from registration data
            unset($data['amount'], $data['payment_method'], $data['payment_status'], $data['payment_date'], $data['attachment'], $data['regItems']);

            // Update total from calculated value
            $data['total'] = $this->data['total'];

            // Update registration order
            $this->record->update($data);

            // Handle registration items
            if (!empty($regItemsData)) {
                // Get existing item IDs
                $existingItemIds = $this->record->regItems->pluck('id')->toArray();
                $updatedItemIds = [];

                foreach ($regItemsData as $itemData) {
                    if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                        // Update existing item
                        $item = $this->record->regItems->find($itemData['id']);
                        if ($item) {
                            $item->update([
                                'product_id' => $itemData['product_id'],
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                            ]);
                            $updatedItemIds[] = $itemData['id'];
                        }
                    } else {
                        // Create new item
                        $newItem = $this->record->regItems()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                        ]);
                        $updatedItemIds[] = $newItem->id;
                    }
                }

                // Delete items that are no longer present
                $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
                if (!empty($itemsToDelete)) {
                    $this->record->regItems()->whereIn('id', $itemsToDelete)->delete();
                }
            }

            // Update or create transaction
            if ($this->record->transaction) {
                $this->record->transaction->update($transactionData);
            } else {
                $this->record->transaction()->create(array_merge($transactionData, [
                    'order_id' => $this->record->id
                ]));
            }

            DB::commit();

            // Flash success message
            // session()->flash('success', 'Registration updated successfully!');
            Notification::make()
                ->title('Registration updated successfully!')
                ->success()
                ->send();

            // Redirect back to list
            $this->redirect(OrderResource::getUrl('index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Failed to update registration: ' . $e->getMessage())
                ->danger()
                ->send();
            // session()->flash('error', 'Failed to update registration: ' . $e->getMessage());
        }
    }

    protected static string $view = 'filament.resources.registration.order-resource.pages.registration-edit';
}
