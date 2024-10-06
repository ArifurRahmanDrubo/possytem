@extends('layout.sidenav-layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-lg-4 p-2">
                <div class="shadow-sm h-100 bg-white rounded-3 p-3">
                    <div class="row">
                        <div class="col-8">
                            <span class="text-bold text-dark">Purhase From </span>
                            <p class="text-xs mx-0 my-1">Name: <span id="SName"></span> </p>
                            <p class="text-xs mx-0 my-1">Email: <span id="SEmail"></span></p>
                            <p class="text-xs mx-0 my-1">User ID: <span id="SId"></span> </p>
                        </div>
                        <div class="col-4">
                            <img class="w-50" src="{{ 'images/logo.png' }}">
                            <p class="text-bold mx-0 my-1 text-dark">Purchase </p>
                            <p class="text-xs mx-0 my-1">Date: {{ date('Y-m-d') }} </p>
                        </div>
                    </div>
                    <hr class="mx-0 my-2 p-0 bg-secondary" />
                    <div class="row">
                        <div class="col-12">
                            <table class="table w-100" id="purchaseTable">
                                <thead class="w-100">
                                    <tr class="text-xs">
                                        <td>Name</td>
                                        <td>Qty</td>
                                        <td>Remove</td>
                                    </tr>
                                </thead>
                                <tbody class="w-100" id="purchaseList">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="mx-0 my-2 p-0 bg-secondary" />
                    <div class="row">
                        <div class="col-12">
                            <p>
                                <button onclick="createpurchase()"
                                    class="btn  my-3 bg-gradient-primary w-40">Confirm</button>
                            </p>
                        </div>
                        <div class="col-12 p-2">

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 p-2">
                <div class="shadow-sm h-100 bg-white rounded-3 p-3">
                    <table class="table  w-100" id="productTable">
                        <thead class="w-100">
                            <tr class="text-xs text-bold">
                                <td>Product</td>
                                <td>Pick</td>
                            </tr>
                        </thead>
                        <tbody class="w-100" id="productList">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 p-2">
                <div class="shadow-sm h-100 bg-white rounded-3 p-3">
                    <table class="table table-sm w-100" id="supplierTable">
                        <thead class="w-100">
                            <tr class="text-xs text-bold">
                                <td>Supplier</td>
                                <td>Pick</td>
                            </tr>
                        </thead>
                        <tbody class="w-100" id="supplierList">

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>




    <div class="modal animated zoomIn" id="create-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Add Product</h6>
                </div>
                <div class="modal-body">
                    <form id="add-form">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 p-1">
                                    <label class="form-label">Product ID *</label>
                                    <input type="text" class="form-control" id="PId" readonly>
                                    <label class="form-label mt-2">Product Name *</label>
                                    <input type="text" class="form-control" id="PName" readonly>
                                    <label class="form-label mt-2">Product Qty *</label>
                                    <input type="text" class="form-control" id="PQty">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>
                    <button onclick="add()" id="save-btn" class="btn bg-gradient-success">Add</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        (async () => {
            showLoader();
            await supplierList();
            await ProductList();
            hideLoader();
        })()


        let purchaseItemList = [];


        function ShowPurchaseItem() {

            let purchaseList = $('#purchaseList');

            purchaseList.empty();

            purchaseItemList.forEach(function(item, index) {
                let row = `<tr class="text-xs">
                        <td>${item['product_name']}</td>
                        <td>${item['qty']}</td>
                        <td><a data-index="${index}" class="btn remove text-xxs px-2 py-1  btn-sm m-0">Remove</a></td>
                     </tr>`
                purchaseList.append(row)
            })

            CalculateGrandTotal();

            $('.remove').on('click', async function() {
                let index = $(this).data('index');
                removeItem(index);
            })

        }


        function removeItem(index) {
            purchaseItemList.splice(index, 1);
            ShowPurchaseItem()
        }

        function CalculateGrandTotal() {

        }


        function add() {
            let PId = document.getElementById('PId').value;
            let PName = document.getElementById('PName').value;
            let PQty = document.getElementById('PQty').value;
            if (PId.length === 0) {
                errorToast("Product ID Required");
            } else if (PName.length === 0) {
                errorToast("Product Name Required");
            } else if (PQty.length === 0) {
                errorToast("Product Quantity Required");
            } else {
                let item = {
                    product_name: PName,
                    product_id: PId,
                    qty: PQty
                };
                purchaseItemList.push(item);
                console.log(purchaseItemList);
                $('#create-modal').modal('hide')
                ShowPurchaseItem();
            }
        }




        function addModal(id, name) {
            document.getElementById('PId').value = id;
            document.getElementById('PName').value = name;
            $('#create-modal').modal('show')
        }


        async function supplierList() {

            let res = await axios.get("/list-supplier", HeaderToken());
            let supplierList = $("#supplierList");
            let supplierTable = $("#supplierTable");
            supplierTable.DataTable().destroy();
            supplierList.empty();
            res.data['rows'].forEach(function(item, index) {
                let row = `<tr class="text-xs">
                        <td><i class="bi bi-person"></i> ${item['name']}</td>
                        <td><a data-name="${item['name']}" data-email="${item['email']}" data-id="${item['id']}" class="btn btn-outline-dark addsupplier  text-xxs px-2 py-1  btn-sm m-0">Add</a></td>
                     </tr>`
                supplierList.append(row);
            })


            $('.addsupplier').on('click', async function() {
                let SName = $(this).data('name');
                let SEmail = $(this).data('email');
                let SId = $(this).data('id');
                $("#SName").text(SName)
                $("#SEmail").text(SEmail)
                $("#SId").text(SId)
            })

            new DataTable('#supplierTable', {
                order: [
                    [0, 'desc']
                ],
                scrollCollapse: false,
                info: false,
                lengthChange: false
            });
        }


        async function ProductList() {
            let res = await axios.get("/list-product", HeaderToken());
            let productList = $("#productList");
            let productTable = $("#productTable");
            productTable.DataTable().destroy();
            productList.empty();

            res.data['rows'].forEach(function(item, index) {
                let row = `<tr class="text-xs">
                        <td> ${item['name']} </td>
                        <td><a data-name="${item['name']}" data-id="${item['id']}" class="btn btn-outline-dark text-xxs px-2 py-1 addProduct  btn-sm m-0">Add</a></td>
                     </tr>`
                productList.append(row)
            })


            $('.addProduct').on('click', async function() {
                let PName = $(this).data('name');
                let PId = $(this).data('id');
                addModal(PId, PName)
            })


            new DataTable('#productTable', {
                order: [
                    [0, 'desc']
                ],
                scrollCollapse: false,
                info: false,
                lengthChange: false
            });
        }


        async function createpurchase() {
            let SId = document.getElementById('SId').innerText;

            // The purchaseItemList already contains all products.
            let Data = {
                "supplier_id": SId,
                "products": purchaseItemList, // Send the complete list of products
            };

            if (SId.length === 0) {
                errorToast("Supplier Required!");
            } else if (purchaseItemList.length === 0) {
                errorToast("Product Required!");
            } else {
                showLoader();
                let res = await axios.post("/purchase-create", Data, HeaderToken());
                hideLoader();
                if (res.data['status'] === "success") {
                    window.location.href = '/purchasePage';
                    successToast("Purchase Created");
                } else {
                    errorToast("Something Went Wrong");
                }
            }
        }
    </script>
@endsection
