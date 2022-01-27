<?php

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!=true) {
    header("location: login.php");
    exit;  
}

include "partials/_dbconnect.php";

$insert = false;
$update = false;
$delete = false;

if (isset($_GET['delete'])) {
  $user_sno = $_GET['delete'];
  $delete = true;
  $sql = "DELETE FROM `notes` WHERE `user_sno` = $user_sno";
  $result = mysqli_query($conn,$sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(isset($_POST['snoEdit'])){
    //Update the record
    // Variables to be inserted into the table
    $user_sno = $_POST['snoEdit'];
    $user_title = $_POST['titleEdit'];
    $user_description = $_POST['descriptionEdit'];

    $sql = "UPDATE `notes` SET `user_title` = '$user_title', `user_description` = '$user_description' WHERE `notes`.`user_sno` = $user_sno";
    $result = mysqli_query($conn,$sql);
    if($result){
      $update = true;
    }
    else{
      echo "WE could not update the note successfully";
    }
  }

  else{
    $email = $_SESSION['email'];
    $user_title = $_POST['title'];
    $user_description = $_POST['description'];

    $sql = "INSERT INTO `notes` (`user_title`, `user_description`, `email`, `dt`) VALUES ('$user_title', '$user_description', '$email', current_timestamp())";
    $result = mysqli_query($conn,$sql);
    
    if ($result) {
      $insert = true;
    }
    else {
      echo "The Note was not inserted successfully because of this error--->".mysqli_error($conn);
    }

  }

}
?>


<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <title>Welcome - <?php echo $_SESSION['email'] ?> </title>
</head>

<body>
    <?php require 'partials/_editModal.php' ?>

    <!-- Navbar start -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/NotesApp">NotesApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="/NotesApp/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact Us</a>
                    </li>
                </ul>
                <li class="nav-item d-flex">
                    <a class="nav-link active text-light" href="#"><span class="text-info"> <b><?php echo $_SESSION['email'] ?> </b></span> </a>
                </li>
                <li class="nav-item d-flex">
                    <a class="nav-link  text-light" href="/NotesApp/logout.php"><span type="button" class="btn btn-outline-success"> Logout </span> </a>
                </li>
                
            </div>
        </div>
    </nav>

    <?php
        if ($insert) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Success!</strong> Your note has been inserted successfully!.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
  ?>
    <?php
      if ($delete) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your note has been deleted successfully!.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
      }
  ?>
    <?php
      if ($update) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your note has been updated successfully!.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
      }
?>

    <div class="container my-4">
        <h2 class="my-4 text-capitalize text-success">add a note to NotesApp</h2>
        <hr>
        <form action="/NotesApp/index.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Note Title</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Note Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Add Note</button>
        </form>
    </div>

    <div class="container my-4">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php
            $email = $_SESSION['email'];
            $sql = "SELECT * FROM `notes` WHERE notes.email = '$email' ";
            $result = mysqli_query($conn, $sql);
            $sno = 0;
            while ($row = mysqli_fetch_assoc($result)) {
              $sno = $sno + 1;
              echo "<tr>
              <th scope = 'row'>".$sno. "</th>
              <td>".$row['user_title']. "</td>
              <td>".$row['user_description']. "</td>
              <td><button class='edit btn btn-sm btn-success' id=".$row['user_sno']." >Edit</button> <button class='delete btn btn-sm btn-danger' id=d".$row['user_sno']." >Delete</button></td>
             </tr>";
            }
            ?>

            </tbody>
        </table>
    </div>

    <?php include 'partials/_footer.php' ?>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
    edits = document.getElementsByClassName('edit');
    Array.from(edits).forEach((element) => {
        element.addEventListener("click", (e) => {
            // console.log("edit", )
            tr = e.target.parentNode.parentNode;
            title = tr.getElementsByTagName('td')[0].innerText;
            description = tr.getElementsByTagName('td')[1].innerText;
            // console.log(title, description);
            titleEdit.value = title;
            descriptionEdit.value = description;
            snoEdit.value = e.target.id;
            // console.log(e.target.id);
            $('#editModal').modal('toggle');
        })
    })

    deletes = document.getElementsByClassName('delete');
    Array.from(deletes).forEach((element) => {
        element.addEventListener("click", (e) => {
            // console.log("deletes", )
            sno = e.target.id.substr(1);

            if (confirm("Are you sure you want to delete this node!")) {
                console.log("yes");
                window.location = `/NotesApp/index.php?delete=${sno}`;

            } else {
                console.log("No");
            }
        })
    })
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>