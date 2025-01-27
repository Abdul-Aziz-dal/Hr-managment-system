<div class="modal fade" id="employeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Employe</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="container-fluid">
                                    <form id="upload-form" action="#" method="POST" enctype="multipart/form-data">
                                   
                                    <input type="hidden" name="name" id="employe_id" hidden>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Employe Name</label>
                                    <input type="text" class="form-control" name="name" id="employeName" placeholder="Enter Name" required>
                                </div>
                            
                                <div class="mb-3">
                                    <label for="email" class="form-label">Employe email</label>
                                    <input type="text" class="form-control" name="email" id="employeEmail" placeholder="Enter Email" required>
                                </div>

                                <div class="mb-3">
                                    <label for="department" class="form-label">Employe Department</label>
                                    <input type="text" class="form-control" name="department" id="employeDepartment" placeholder="Enter Department" required>
                                </div>

                                <div class="mb-3">
                                    <label for="Manager" class="form-label">Employe Manager</label>
                                    <input type="text" class="form-control" name="Manager" id="employeManager" placeholder="Enter Manager" required>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Employe Status</label>
                                    <select class="form-control" name="status" id="employeStatus" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                    </select>
                                </div>

                                    <div class="mb-3">
                                            <label for="file" class="form-label">Chose File</label>
                                            <input class="form-control" type="file" name="file" id="employeFile"   accept=".tar,.gz,.zip" required>
                                        </div>
                                    </form>
                                </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="submitEmployeForm" class="btn btn-primary">
          <i class="fa fa-upload"></i> Save
        </button>
        </div>
    </div>
  </div>
</div>