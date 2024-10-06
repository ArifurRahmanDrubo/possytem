<!-- Modal -->
<div class="modal animated zoomIn" id="details-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Purchase</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="purchase" class="modal-body p-3">
                    <div class="container-fluid">
                        <br/>
                        <div class="row">
                            <div class="col-8">
                                <span class="text-bold text-dark">Purchase From </span>
                                <p class="text-xs mx-0 my-1">Name:  <span id="SName"></span> </p>
                                <p class="text-xs mx-0 my-1">Email:  <span id="SEmail"></span></p>
                                <p class="text-xs mx-0 my-1">User ID:  <span id="SId"></span> </p>
                            </div>
                            <div class="col-4">
                                <img class="w-40" src="{{"images/logo.png"}}">
                                <p class="text-bold mx-0 my-1 text-dark">Purchase  </p>
                                <p class="text-xs mx-0 my-1">Date: {{ date('Y-m-d') }} </p>
                            </div>
                        </div>
                        <hr class="mx-0 my-2 p-0 bg-secondary"/>
                        <div class="row">
                            <div class="col-12">
                                <table class="table w-100" id="purchaseTable">
                                    <thead class="w-100">
                                    <tr class="text-xs text-bold">
                                        <td>Name</td>
                                        <td>Qty</td>
                                        
                                    </tr>
                                    </thead>
                                    <tbody  class="w-100" id="purchaseList">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="mx-0 my-2 p-0 bg-secondary"/>                  
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-primary" data-bs-dismiss="modal">Close</button>
                <button onclick="PrintPage()" class="btn bg-gradient-success">Print</button>
            </div>
        </div>
    </div>
</div>


<script>


    async function purchaseDetails(sup_id,pur_id) {
        showLoader()
        let res=await axios.post("/purchase-details",{sup_id:sup_id,pur_id:pur_id},HeaderToken())
        hideLoader();
         console.log(res);
        document.getElementById('SName').innerText=res.data['rows']['supplier']['name']
        document.getElementById('SId').innerText=res.data['rows']['supplier']['user_id']
        document.getElementById('SEmail').innerText=res.data['rows']['supplier']['email']
        let purchaseList=$('#purchaseList');

        purchaseList.empty();

        res.data['rows']['product'].forEach(function (item,index) {
            let row=`<tr class="text-xs">
                        <td>${item['product']['name']}</td>
                        <td>${item['qty']}</td>
                       
                     </tr>`
                     purchaseList.append(row)
        });



        $("#details-modal").modal('show')
    }



    function PrintPage() {
        let printContents = document.getElementById('purchase').innerHTML;
        let originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        setTimeout(function() {
            location.reload();
        }, 1000);
    }


</script>