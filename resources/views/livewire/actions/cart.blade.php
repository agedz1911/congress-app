<div>
    <section class="breadcrumbs relative pb-0">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0059A8]/10 to-[#0059A8]/80"></div>
        <div class="py-16 lg:py-28 text-center relative">
            <h2 class="text-white uppercase text-2xl font-semibold tracking-wide lg:text-4xl">Cart</h2>
        </div>
    </section>

    <div class="justify-center flex mt-5 lg:mt-10">
        <ul class="steps w-full steps-vertical lg:steps-horizontal">
            <li class="step step-primary">Order Summary</li>
            <li class="step">Detail Participant</li>
            <li class="step">Payment Method</li>
            <li class="step">Review & Order</li>
        </ul>
    </div>
    <section class="py-5 lg:py-10">
        <div class="flex flex-col lg:flex-row justify-between px-5 lg:px-10 py-5 lg:py-10">

            <div class="overflow-x-auto w-full max-w-4xl rounded-2xl ">
                <table class="table">
                    <!-- head -->
                    <thead class="bg-slate-200">
                        <tr>
                            <th style="width: 40%;">Category Product</th>
                            <th>Price</th>
                            <th style="width: 10%;">Quantity</th>
                            <th>Unit Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- row 1 -->
                        <tr>
                            <td>
                                <div>
                                    <p class="font-semibold">Specialist <br>
                                        <span class="font-normal text-xs">Early Bird</span> <br>
                                        <span class="font-normal text-xs">Symposium</span>
                                    </p>
                                </div>
                            </td>
                            <td>1.000.000</td>
                            <td>
                                <input type="number" placeholder="0" min="1" class="input input-sm" />
                            </td>
                            <td>1.000.000</td>
                            <td>
                                <button class="btn btn-xs">
                                    <i class="fa fa-trash text-error text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Workshop 1</td>
                            <td>700.000</td>
                            <td>
                                <input type="number" placeholder="0" min="1" class="input input-sm" />
                            </td>
                            <td>1.000.000</td>
                            <td>
                                <button class="btn btn-xs">
                                    <i class="fa fa-trash text-error text-xs"></i>
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <button class="btn btn-error rounded-lg mt-4"><i class="fa fa-angles-left text-xs"></i> Back to Product Registration</button>
            </div>

            <div>
                <h4 class="text-xl font-semibold mb-3">Order Summary</h4>
                <div class="card w-96 max-w-xl card-md shadow-sm">
                    <div class="card-body">
                        <div class="flex justify-between">
                            <h2 class="card-title">Subtotal</h2>
                            <h2 class="card-title">1.000.000</h2>
                        </div>
                        <h2 class="text-lg font-semibold">Promo Code</h2>
                        <div class="join mb-4">
                            <div>
                                <label class="input validator join-item">
                                    <i class="fa fa-tag text-primary mr-1"></i>
                                    <input type="text" placeholder="promo code" />
                                </label>
                                <div class="validator-hint hidden">Enter valid Code</div>
                            </div>
                            <button class="btn btn-accent join-item">Apply</button>
                        </div>
                        <div class="flex justify-between">
                            <h2 class="card-title">Discount</h2>
                            <h2 class="card-title">-100.000</h2>
                        </div>
                        <div class="flex justify-between mb-4">
                            <h2 class="card-title">Total</h2>
                            <h2 class="card-title text-success">1.000.000</h2>
                        </div>
                        <p></p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-primary btn-block">Continue to Payment Method <i class="fa fa-angles-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="p-5 lg:p-10">
        <div class="card w-full bg-base-100 shadow-sm">
            <div class="card-body">
                <span class="badge badge-xs badge-warning">Details</span>
                <div class="w-full flex justify-between flex-col lg:flex-row py-5">
                    <div class="form-control w-full">
                        <label class="label p-4 hover:border hover:border-primary hover:rounded-xl w-full cursor-pointer">
                            <input type="radio" name="radio-4" class="radio radio-primary" checked="checked" />
                            <span>Register for my Self <i class="fa fa-credit-card"></i></span>
                        </label>
                    </div>
                    <div class="form-control w-full">
                        <label class="label p-4 hover:border hover:border-primary hover:rounded-xl w-full  cursor-pointer">
                            <input type="radio" name="radio-4" class="radio radio-primary" />
                            <span>Register for others person <i class="fa fa-money-bill-transfer"></i></span>
                        </label>
                    </div>
                </div>
                <div class="mt-6">
                    
                </div>
            </div>
        </div>
        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4">
            <legend class="fieldset-legend">Page details</legend>

            <label class="label">Title</label>
            <input type="text" class="input" placeholder="My awesome page" />

            <label class="label">Slug</label>
            <input type="text" class="input" placeholder="my-awesome-page" />

            <label class="label">Author</label>
            <input type="text" class="input" placeholder="Name" />
        </fieldset>
    </section>


    <section class="py-5 lg:py-10 flex flex-col items-center">
        <div class="card w-full max-w-3xl bg-base-100 card-lg shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Select Payment Method</h2>
                <div class="w-full flex justify-between flex-col lg:flex-row py-5">
                    <div class="form-control w-full">
                        <label class="label p-4 hover:border hover:border-primary hover:rounded-xl w-full cursor-pointer">
                            <input type="radio" name="radio-4" class="radio radio-primary" checked="checked" />
                            <span>Credit Card <i class="fa fa-credit-card"></i></span>
                        </label>
                    </div>
                    <div class="form-control w-full">
                        <label class="label p-4 hover:border hover:border-primary hover:rounded-xl w-full  cursor-pointer">
                            <input type="radio" name="radio-4" class="radio radio-primary" />
                            <span>Bank Transfer <i class="fa fa-money-bill-transfer"></i></span>
                        </label>
                    </div>
                </div>
                <div class="justify-between card-actions">
                    <button class="btn btn-error"><i class="fa fa-angles-left text-xs"></i> Back to Summary</button>
                    <button class="btn btn-primary">Continue <i class="fa fa-angles-right text-xs"></i></button>
                </div>
            </div>
        </div>
    </section>

    <section class="px-5 lg:px-10 py-5 lg:py-10">
        <div>
            <h2 class="font-semibold text-xl">Order Review</h2>
            <div class="flex flex-col lg:flex-row justify-between ">
                <div class="overflow-x-auto w-full max-w-4xl rounded-2xl ">
                    <table class="table">
                        <!-- head -->
                        <thead class="bg-slate-200">
                            <tr>
                                <th style="width: 40%;">Category Product</th>
                                <th>Price</th>
                                <th style="width: 10%;">Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <td>
                                    <div>
                                        <p class="font-semibold">Specialist <br>
                                            <span class="font-normal text-xs">Early Bird</span> <br>
                                            <span class="font-normal text-xs">Symposium</span>
                                        </p>
                                    </div>
                                </td>
                                <td>1.000.000</td>
                                <td>
                                    1
                                </td>
                                <td>1.000.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <h4 class="text-xl font-semibold mb-3">Order Summary</h4>
                    <div class="card w-96 max-w-xl card-md shadow-sm">
                        <div class="card-body">
                            <div class="flex justify-between">
                                <h2 class="card-title">Subtotal</h2>
                                <h2 class="card-title">1.000.000</h2>
                            </div>

                            <div class="flex justify-between">
                                <h2 class="card-title">Promo code</h2>
                                <h2 class="card-title">Discount10</h2>
                            </div>
                            <div class="flex justify-between">
                                <h2 class="card-title">Discount</h2>
                                <h2 class="card-title">-100.000</h2>
                            </div>
                            <div class="flex justify-between mb-4">
                                <h2 class="card-title">Total</h2>
                                <h2 class="card-title text-success">1.000.000</h2>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h1 class="text-xl font-semibold">Selected Payment Method:</h1>
                <h4 class="text-primary">Credit Card <i class="fa fa-credit-card"></i></h4>
            </div>

            <div class="card bg-base-100 mt-5 shadow-sm">
                <div class="card-body">
                    <h1 class="mb-3 card-title font-semibold">Terms and Conditions</h1>
                    <p class="text-justify text-gray-400 text-sm">Lorem ipsum dolor sit amet, consectetur
                        adipisicing elit. In impedit itaque debitis reprehenderit assumenda. Esse aspernatur a in. Sunt
                        provident quisquam repudiandae voluptates possimus optio reiciendis consectetur molestias
                        repellat fugit.</p>
                    <h1 class="mb-3 mt-5 card-title font-semibold">Cancellation Policy</h1>
                    <p class="text-justify text-gray-400 text-sm">Lorem ipsum dolor sit amet, consectetur
                        adipisicing elit. In impedit itaque debitis reprehenderit assumenda. Esse aspernatur a in. Sunt
                        provident quisquam repudiandae voluptates possimus optio reiciendis consectetur molestias
                        repellat fugit.</p>

                    <div class="flex flex-col justify-center items-center py-10">
                        <h1 class="text-center text-lg mb-4">By clicking "I Agree" you agree and consent to our Terms
                            and Conditions.</h1>
                        <div class="form-control">
                            <label class="label cursor-pointer gap-4">
                                <input type="checkbox" class="checkbox checkbox-md" />
                                <span class="label-text">I agree to the terms and conditions</span>
                            </label>
                        </div>

                        <div class="flex w-full max-w-xl justify-between mt-10 gap-4">
                            <button class="btn btn-error">
                                <i class="fa fa-angles-left text-xs"></i>
                                Back to Payment
                            </button>
                            <button class="btn btn-primary">
                                Submit Order
                                <i class="fa fa-anles-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>