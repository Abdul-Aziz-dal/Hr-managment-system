<?php 
session_start();
if(!$_SESSION["user_id"]) header("location:../login.php");

require_once('../googleApi.config/config.php');
require_once("../googleApi.config/GoogleDriveUploadAPI.php");
$gdriveAPI = new GoogleDriveUploadAPI();
?>

<!doctype html>
<html lang="en">

<!-- <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hr Management System</title>
</head> -->

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP - Upload File in Gdrive</title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/seodashlogo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js" integrity="sha512-naukR7I+Nk6gp7p5TMA4ycgfxaZBJ7MO5iC3Fp6ySQyKFHOGfpkSZkYVWV5R7u7cfAicxanwYQ5D1e17EfJcMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <!-- <script src="../assets/js/script.js"></script> -->
</head>

<body>

    <!--  Main wrapper -->
   <div class="container mt-5">      
    <div class="row">      
       


                <!-- Modal -->
                <?php require('../ui/employeAddForm.php'); ?>
                <!-- End of modal -->

               <!-- getting google driv files -->
               <!-- <?php require('../ui/getResouresFromDrive.php'); ?> -->
                <!-- End getting google driv files -->



      <!-- Data table -->
  <div class="row mb-3">
    <!-- Search Input -->
    <div class="col-md-6">
      <input type="text" id="searchInput" class="form-control" placeholder="Search here...">
    </div>

        <!-- Open Add Emplye form -->
        <div class="col-md-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeModal">
        Add Employe
        </button>
        </div>

     <div class="col-md-3">
        <a href="../apis/logout.php" class="btn btn-danger">
        Logout
        </a>
      </div>

   </div>
            
        <!-- Table and pagination -->
        <?php require('../ui/dataTable.php'); ?>
          <!-- End Table and pagination-->
        </div>

       <!-- End Data table -->
       <!-- https://drive.google.com/file/d/16CCuJ12ywZ6R-mdqhJoGRo4vqv1a1ePl/view -->
       <!-- https://drive.usercontent.google.com/u/0/uc?id=16CCuJ12ywZ6R-mdqhJoGRo4vqv1a1ePl&export=download -->
        
      </div>
    </div>
  </div>

   </div>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/js/dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
  let data = [];
  const rowsPerPage = 5;
  let currentPage = 1;
  function fetchData() {
    $.ajax({
    url: 'http://localhost/php-gdrive-upload/apis/getEmployesData.php',
    method: 'GET',
    dataType: 'json',
    success: function (Response) {
      if (Response && Response.status === 'success') {
        if (Array.isArray(Response.data) && Response.data.length > 0) {
          data = Response.data;
        displayRows(); 
      } else {
        data = []
        displayRows();
        console.warn(Response.message || 'No data available.');
      }
    } else {
            console.error('Error:', Response.message || 'Unexpected response status');
        }
    },
    error: function (xhr, status, error) {
      console.error(xhr,'Error fetching data:', error);
      alert(error)
    }
  });
  }
   fetchData();

  // Function to display rows
  function displayRows() {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const filteredData = getFilteredData();

    filteredData.slice(start, end).forEach((row,index) => {
      const tr = `<tr>
        <td>${index + 1}</td>
        <td>${row.employe_name}</td>
        <td>${row.employe_email}</td>
        <td>${row.employe_department}</td>
        <td>${row.employe_manager}</td>
        <td>${row.employe_status == 1 ? 'Active' : 'Inactive'}</td>
        <td>
            <a href="${`https://drive.google.com/file/d/${row.employe_file_path}/view`}" target="_blank"  class="btn btn-info btn-sm">View document</a>
            <a href="${`https://drive.usercontent.google.com/u/0/uc?id=${row.employe_file_path}&export=download`}" class="btn btn-info btn-sm">download document</a>
            <button 
            class="btn btn-warning btn-sm employe-edit-btn" 
            data-bs-toggle="modal" 
            data-bs-target="#employeModal"
            data-name="${row.employe_name}"
            data-email="${row.employe_email}"
            data-department="${row.employe_department}"
            data-manager="${row.employe_manager}"
            data-status="${row.employe_status}"
            data-id="${row.employe_id}"
            >
            Edit
            </button>
        </td>

    </tr>`;
   
      tableBody.insertAdjacentHTML("beforeend", tr);
    });
    updatePagination(filteredData.length);
  }

  // Function to update pagination
  function updatePagination(totalRows) {
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";

    const totalPages = Math.ceil(totalRows / rowsPerPage);

    for (let i = 1; i <= totalPages; i++) {
      const li = `<li class="page-item ${i === currentPage ? "active" : ""}">
        <button class="page-link" onclick="goToPage(${i})">${i}</button>
      </li>`;
      pagination.insertAdjacentHTML("beforeend", li);
    }
  }

  // Function to navigate to a page
  function goToPage(page) {
    currentPage = page;
    displayRows();
  }

  // Function to get filtered data
  function getFilteredData() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    return data.filter(row =>
      row.employe_name.toLowerCase().includes(searchInput) ||
      row.employe_department.toLowerCase().includes(searchInput)
    );
  }

  // Event listener for search
  document.getElementById("searchInput").addEventListener("input", () => {
    currentPage = 1; // Reset to the first page
    displayRows();
  });


  $(document).ready(function () {
  $('#submitEmployeForm').click(function () {
    try {
      const isUpdate = !!$('#employe_id').val();
        const $button = $(this);
        $buttonText=isUpdate?'Updated':'Save';
        $button.html(`${$buttonText} <div class="spinner-border text-light ms-2" style="width:12px; height:12px;" role="status"><span class="visually-hidden">Loading...</span></div>`);
        $button.prop('disabled', true);

        const formData = new FormData();
        formData.append('employeName', $('#employeName').val());
        formData.append('employeEmail', $('#employeEmail').val());
        formData.append('employeDepartment', $('#employeDepartment').val());
        formData.append('employeManager', $('#employeManager').val());
        formData.append('employeStatus', $('#employeStatus').val());
        formData.append('employeFile', $('#employeFile')[0]?.files[0] || '');
        
        if (isUpdate) {
            formData.append('employeId', $('#employe_id').val());
        }

        const url = isUpdate 
            ? 'http://localhost/php-gdrive-upload/apis/employe-update.php' 
            : 'http://localhost/php-gdrive-upload/apis/upload.php';

            
        // AJAX request to upload or update data
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
               if(response.success){
                 console.log("response ",response)
                   fetchData();
                   resetForm();
                  }
                  $button.html($buttonText)
                  $button.prop('disabled', false);
                  alert(response.message);
                },
            error: function (xhr, status, error) {
              $button.html($buttonText)
              $button.prop('disabled', false);
              alert(`An error occurred while processing the request: ${error}`);
            }

            
        });
    } catch (e) {
        alert(`An error occurred: ${e.message}`);
    }
});

  function resetForm() { 
    $('#employeName').val('');
    $('#employeEmail').val('');
    $('#employeDepartment').val('');
    $('#employeManager').val('');
    $('#employeFile').val('');
    $('#employe_id').val('');
  }

  $('body').on('click', '.employe-edit-btn', function () {
        const button = $(this);

        const name = button.data('name');
        const email = button.data('email');
        const department = button.data('department');
        const manager = button.data('manager');
        const status = button.data('status');
        const id = button.data('id');

        $('#employe_id').val(id);
        $('#employeName').val(name);
        $('#employeEmail').val(email);
        $('#employeDepartment').val(department);
        $('#employeManager').val(manager);
        // $('#employeFile').val('');
        $('#employeStatus').val(status);
        $('#submitEmployeForm').text('Update');

        //when Modal close do reset form
        $('#employeModal').on('hidden.bs.modal', function () {
          resetForm()
        // $('#employeFile').val('');
        $('#submitEmployeForm').text('Save');
    });


    });
});


</script>

</body>

</html>