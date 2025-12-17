<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">

        {{-- Order Summary Card --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <div class="flex justify-between">
                    <h2 class="card-title text-2xl mb-4">Order Details</h2>
                    <div class="badge badge-error">sdfsdfsdf</div>
                    
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="text-sm text-gray-500">Registration Code</label>
                        <p class="font-mono font-bold text-lg">sdfsdfs</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <p>
                            <span class="badge badge-primary"></span>
                           
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total Amount</label>
                        <p class="font-bold text-lg">IDR fdsf</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Payment Method</label>
                        <p class="font-semibold">dsfsdfsd</p>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="divider">Order Items</div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td>dsfsdf</td>
                                <td>sdfsdf</td>
                                <td>IDR sdfsdf</td>
                                <td>IDR sfsdf </td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="flex justify-center gap-4 mt-8">
            <a href="{{ route('myregistrations') }}" class="btn btn-outline">Back to Dashboard</a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Order
            </button>
            <button class="btn btn-ghost"><i class="fa fa-file-upload"></i> Payment Confirmation</button>
        </div>
    </div>
</div>