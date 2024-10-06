<div class="modal animated zoomIn" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Supplier</h5>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label class="form-label">Supplier Name *</label>
                                <input type="text" class="form-control" id="supplierNameUpdate">

                                <label class="form-label mt-3">Supplier Email *</label>
                                <input type="text" class="form-control" id="supplierEmailUpdate">

                                <label class="form-label mt-3">Supplier Mobile *</label>
                                <input type="text" class="form-control" id="supplierMobileUpdate">

                                <input type="text" class="d-none" id="updateID">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="update-modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                <button onclick="Update()" id="update-btn" class="btn bg-gradient-success" >Update</button>
            </div>
        </div>
    </div>
</div>


<script>

    async function FillUpUpdateForm(id){
        try {
            document.getElementById('updateID').value=id;
            showLoader();
            let res=await axios.post("/supplier-by-id",{id:id},HeaderToken())
            hideLoader();
            document.getElementById('supplierNameUpdate').value=res.data['rows']['name'];
            document.getElementById('supplierEmailUpdate').value=res.data['rows']['email'];
            document.getElementById('supplierMobileUpdate').value=res.data['rows']['mobile'];
        }catch (e) {
            unauthorized(e.response.status)
        }
    }



    async function Update() {

        try {
            let supplierName = document.getElementById('supplierNameUpdate').value;
            let supplierEmail = document.getElementById('supplierEmailUpdate').value;
            let supplierMobile = document.getElementById('supplierMobileUpdate').value;
            let updateID = document.getElementById('updateID').value;

            document.getElementById('update-modal-close').click();
            showLoader();
            let res = await axios.post("/update-supplier",{name:supplierName,email:supplierEmail,mobile:supplierMobile,id:updateID},HeaderToken())
            hideLoader();

            if(res.data['status']==="success"){
                document.getElementById("update-form").reset();
                successToast(res.data['message'])
                await getList();
            }
            else{
                errorToast(res.data['message'])
            }

        }catch (e) {
            unauthorized(e.response.status)
        }


    }

</script>
