<?php include '../template/header.php';

if ($_SESSION['status'] != 'login') {
    header('location:../index.php');
}

$usernamesesi = $_SESSION['username'];
//echo $usernamesesi;
$data = mysqli_query($koneksi2, "SELECT * FROM users INNER JOIN customers ON users.user_id = customers.user_id where users.username = '$usernamesesi'");
$data = mysqli_fetch_assoc($data);


//OFFICE

//query total pegawai
$total_employees = mysqli_query($koneksi, "SELECT COUNT(*) FROM employees");
$total_employees = mysqli_fetch_array($total_employees);

if ($total_employees == NULL) {
    $total_employees = 'No Employees Available';
} else {
    $total_employees = $total_employees[0];
}

//query search president

$president_name = mysqli_query($koneksi, "SELECT * FROM employees WHERE jobTitle='President'");
$president_name = mysqli_fetch_array($president_name);

//query amount of payment

$total_payment = mysqli_query($koneksi, "SELECT SUM(amount) as total_income FROM payments");
$total_payment = mysqli_fetch_assoc($total_payment);

//query total target product vendor

$total_product_vendor = mysqli_query($koneksi, "SELECT COUNT(DISTINCT productVendor) as totalProductVendor FROM products");
$total_product_vendor = mysqli_fetch_assoc($total_product_vendor);

//query get all employees

$halaman = 10; //batasan halaman
$page = isset($_GET['halaman']) ? (int)$_GET["halaman"] : 1;
$mulai = ($page > 1) ? ($page * $halaman) - $halaman : 0;

$prev = $page - 1;
$next = $page + 1;

$result = mysqli_query($koneksi, "SELECT * FROM employees");
$total = mysqli_num_rows($result);
$pages = ceil($total / $halaman);

$get_all_employees = mysqli_query(
    $koneksi,
    "SELECT * FROM employees INNER JOIN offices ON offices.officeCode = employees.officeCode
    LIMIT $mulai, $halaman"
);




//query get reportsTo employees

$reportsTo_employees = mysqli_query($koneksi, "SELECT reportsTo FROM employees");

//CUSTOMER

//query total CUSTOMER
$total_customers = mysqli_query($koneksi, "SELECT COUNT(*) FROM customers");
$total_customers = mysqli_fetch_array($total_customers);

if ($total_customers == NULL) {
    $total_customers = 'No Customers Available';
} else {
    $total_customers = $total_customers[0];
}

//query customers which have highest limit

$customer_highest_limit = mysqli_query($koneksi, "SELECT * FROM customers ORDER BY creditLimit DESC LIMIT 1");
$customer_highest_limit = mysqli_fetch_array($customer_highest_limit);

//query total order by highest limit
$customer_number_highest_limit = $customer_highest_limit['customerNumber'];
$customer_order = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) FROM orders INNER JOIN customers ON orders.customerNumber = customers.customerNumber 
    WHERE orders.customerNumber='$customer_number_highest_limit' AND orders.status='Shipped'"
);
$customer_order = mysqli_fetch_array($customer_order);

//query total uang pesanan oleh user credit limit tertinggi

$customer_total_income = mysqli_query(
    $koneksi,
    "SELECT SUM(amount) as customer_payment FROM payments INNER JOIN customers ON payments.customerNumber = customers.customerNumber 
    WHERE payments.customerNumber='$customer_number_highest_limit'"
);

$customer_total_income = mysqli_fetch_array($customer_total_income);



function bmi()
{
    $hasil = "";
    if (isset($_POST['submit'])) {
        $kelamin   = $_POST['kelamin'];
        $tb        = $_POST['tb'];
        $bb        = $_POST['bb'];
        if (($kelamin != NULL) && ($tb != NULL) && ($tb >= 110) && ($bb != NULL) && ($bb >= 12)) {



            // Input form

            /* Rumus BMI adalah:
            BMI = BERAT BADAN / KUADRAT TINGGI BADAN m
            */
            $tb = $tb / 100;
            $tbcm = $tb * 100;
            // Hitung BMI

            $bmia        = $bb / ($tb * $tb);
            $bmi         = number_format($bmia, 2);

            /*Hitung BB ideal sesuai jenis kelamin dengan rumus broca
        
            notes : tinggi badan dalam cm
            wanita = (tinggi badan - 100 )-(15%*(tinggi badan-100))
            pria = (tinggi badan - 100 )-(10%*(tinggi badan-100))
            */

            if ($kelamin == "Perempuan") {
                $bbideal = ($tbcm - 100) - (0.15 * ($tbcm - 100));
            } else if ($kelamin == "Laki-laki") {
                $bbideal = ($tbcm - 100) - (0.1 * ($tbcm - 100));
            }

            if ($bmi < 16) {
                $hasil = "Berat Banda Anda Masuk Kategori Sangat Kurus. Berat Badan Anda Seharusnya $bbideal";
            } elseif ($bmi >= 16.00 && $bmi <= 16.99) {
                $hasil = "Berat Badan Anda Masuk Kategori Kurus. Berat Badan Anda Seharusnya $bbideal";
            } elseif ($bmi >= 17.00 && $bmi <= 18.49) {
                $hasil = "Berat Badan Anda Masuk Kategori Sedikit Kurus. Berat Badan Anda Seharusnya $bbideal";
            } elseif ($bmi >= 18.50 && $bmi <= 24.99) {
                $hasil = "Berat Badan Anda Masuk Kategori Normal. Berat Badan Anda Seharusnya $bb";
            } elseif ($bmi >= 25 && $bmi <= 29.99) {
                $hasil = "Berat Badan Anda Masuk Kategori Kelebihan Berat Badan. Berat Badan Anda Seharusnya $bbideal";
            } elseif ($bmi >= 30 && $bmi <= 34.99) {
                $hasil = "Berat Badan Anda Masuk Kategori Obesitas Kelas 1. Berat Badan Anda Seharusnya $bbideal";
            } elseif ($bmi >= 35 && $bmi <= 39.99) {
                $hasil = "Berat Badan Anda Masuk Kategori Obesitas Kelas 2. Berat Badan Anda Seharusnya $bbideal";
            } else {
                $hasil = "Berat Badan Anda Masuk Kategori Obesitas Kelas 3. Berat Badan Anda Seharusnya $bbideal";
            }
        }
    }
    return $hasil;
}

class ComplexCalculator
{
    private $realPart;
    private $imaginaryPart;

    public function __construct($realPart, $imaginaryPart)
    {
        $this->realPart = $realPart;
        $this->imaginaryPart = $imaginaryPart;
    }

    public function add($number)
    {
        $resultRealPart = $this->realPart + $number->realPart;
        $resultImaginaryPart = $this->imaginaryPart + $number->imaginaryPart;
        return new ComplexCalculator($resultRealPart, $resultImaginaryPart);
    }

    public function subtract($number)
    {
        $resultRealPart = $this->realPart - $number->realPart;
        $resultImaginaryPart = $this->imaginaryPart - $number->imaginaryPart;
        return new ComplexCalculator($resultRealPart, $resultImaginaryPart);
    }

    public function multiply($number)
    {
        $resultRealPart = ($this->realPart * $number->realPart) - ($this->imaginaryPart * $number->imaginaryPart);
        $resultImaginaryPart = ($this->realPart * $number->imaginaryPart) + ($this->imaginaryPart * $number->realPart);
        return new ComplexCalculator($resultRealPart, $resultImaginaryPart);
    }

    public function divide($number)
    {
        $denominator = pow($number->realPart, 2) + pow($number->imaginaryPart, 2);
        $resultRealPart = (($this->realPart * $number->realPart) + ($this->imaginaryPart * $number->imaginaryPart)) / $denominator;
        $resultImaginaryPart = (($this->imaginaryPart * $number->realPart) - ($this->realPart * $number->imaginaryPart)) / $denominator;
        return new ComplexCalculator($resultRealPart, $resultImaginaryPart);
    }

    public function getFormattedResult()
    {
        $sign = ($this->imaginaryPart < 0) ? '-' : '+';
        return "{$this->realPart} {$sign} {$this->imaginaryPart}i";
    }
}



?>
<!-- Content Row -->
<main>
    <div class="content-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Area Chart -->
                
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Profile Overview</h6>

                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <section>
                                <div class="container py-5">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center">
                                                    <img src="../assets/image/user.png" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                                                    <h5 class="my-3"><?php echo $data['username'] ?></h5>
                                                    <p class="text-muted mb-1"><?php echo $data['level'] ?></p>
                                                    <p class="text-muted mb-4"><?php echo $data['phone_number'] ?></p>
                                                    <div class="d-flex justify-content-center mb-2">
                                                        <button type="button" class="btn btn-primary">Edit</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-lg-8">
                                            <div class="card mb-4">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <p class="mb-0">Full Name</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <p class="text-muted mb-0"><?php echo $data['username'] ?></p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <p class="mb-0">Email</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <p class="text-muted mb-0"><?php echo $data['email'] ?></p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <p class="mb-0">Phone</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <p class="text-muted mb-0"><?php echo $data['phone_number'] ?></p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <p class="mb-0">Address</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <p class="text-muted mb-0"><?php echo $data['alamat'] ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>

                <!-- Pie Chart -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Calculate Your Index BMI</h6>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <form method="POST" action="week1.php" class="appointment-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="bb" class="form-control" placeholder="Berat Badan" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="tb" class="form-control" placeholder="Tinggi Badan" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-field">
                                            <div class="select-wrap">
                                                <select name="kelamin" class="form-control">
                                                    <option value="">Jenis kelamin</option>
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row"><br></div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="submit" name="submit" value="Hitung BMI" class="btn btn-primary py-3 px-4" required>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <?php
                                $hasil = bmi();
                                // Tampilkan hasil di bawah formulir jika sudah ada
                                if ($hasil !== "") {
                                    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                    $hasil
                                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                      <span aria-hidden='true'>&times;</span>
                                    </button>
                                  </div>";
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Daily Podcast</h6>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <h5 class="font-weight-light"><b>Senggang Bersama Podcast 13: Menjaga Kesehatan Mental</b></h3>
                            <div class="text-white mb-3"><span class="text-black-opacity-05"><small>By Tangan Belang<span class="sep">/</span> 18 February 2020 <span class="sep">/</span> 28:28</small></span></div>
                            <p class="mb-4">Apakah kebahagiaan bisa mengatasi masalah? Bagaimana cara kami menjaga kesehatan mental di era digital saat ini? Sumber: <a target="_blank" href="https://www.youtube.com/watch?v=OrY0CfvY6TI&t=3s">Link</a></p>
                            <p class="mb-4"></p>
                            <div class="player">
                                <audio id="player2" preload="none" controls style="max-width: 100%">
                                    <source src="../assets/audio/Senggang Bersama Podcast 13 Menjaga Kesehatan Mental.mp3" type="audio/mp3">
                                </audio>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
            </div>
            <hr>
            <!-- Content OFFICE Information Row -->
            <div class="card">
                <h5 class="card-header">
                    <a class="collapsed d-block" data-toggle="collapse" href="#collapse-collapsed-office" aria-expanded="true" aria-controls="collapse-collapsed-office" id="heading-collapsed">
                        <i class="fa fa-chevron-down pull-right"></i>
                        Offices Information
                    </a>
                </h5>
                <div id="collapse-collapsed-office" class="collapse show" aria-labelledby="heading-collapsed">
                    <div class="row card-body">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total of Employees</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_employees; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Name of President</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $president_name['firstName'] . " " . $president_name['lastName']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="far fa-id-badge fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Target of Product Vendor</div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $total_product_vendor['totalProductVendor'] . "/100"  ?></div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $total_product_vendor['totalProductVendor'] ?>%" aria-valuenow="<?php echo $total_product_vendor['totalProductVendor'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">The Office Income</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo "Rp. " . number_format($total_payment['total_income'], 2, ".", ".") ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <br>
            <!-- Content CUSTOMER InformationRow -->

            <div class="card">
                <h5 class="card-header">
                    <a class="collapsed d-block" data-toggle="collapse" href="#collapse-collapsed-customer" aria-expanded="true" aria-controls="collapse-collapsed-customer" id="heading-collapsed">
                        <i class="fa fa-chevron-down pull-right"></i>
                        Customers Information
                    </a>
                </h5>
                <div id="collapse-collapsed-customer" class="collapse show" aria-labelledby="heading-collapsed">
                    <div class="row card-body">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total of Customers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_customers; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Customer Have The Highest Limit</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $customer_highest_limit['customerName']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="far fa-id-badge fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total of Order By Customer</div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $customer_order[0] . "/100"  ?></div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $customer_order[0] ?>%" aria-valuenow="<?php echo $customer_order[0] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">The Customer Spend</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo "Rp. " . number_format($customer_total_income['customer_payment'], 2, ".", ".") ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <br>

            <!-- Content Row -->

            <div class="row">
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Tables</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Employees Data</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="text-align:center" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Name</th>
                                            <th rowspan="2">Email</th>
                                            <th colspan="2">Office</th>
                                            <!-- <th rowspan="2">Report To</th> -->
                                            <th rowspan="2">Position</th>
                                            <th colspan="2" rowspan="2">Action</th>
                                        </tr>
                                        <tr>
                                            <th>Code</th>
                                            <th>City</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        while ($employees = mysqli_fetch_array($get_all_employees)) {

                                            echo
                                            '<tr>
                                            <td>' . $employees['firstName'] . ' ' . $employees['lastName'] . '</td>
                                            <td>' . $employees['email'] . '</td>
                                            <td>' . $employees['officeCode'] . '</td>
                                            <td>' . $employees['city'] . '</td>
                                            <td>' . $employees['jobTitle'] . '</td>'
                                        ?>
                                            <form action="sql_query.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $employees['employeeNumber'] ?>" class="form-control">

                                                <td>
                                                    <a href="update_data.php?id=<?php echo $employees['employeeNumber'] ?>" onclick="return confirm('Are you sure you want to edit this item?')">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                </td>
                                                <td>
                                                    <button type="submit" name="action" value="delete" class="btn btn-success" onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="fa fa-remove"></i>
                                                    </button>
                                                </td>
                                            </form>
                                            </tr>
                                        <?php
                                        }


                                        ?>
                                    </tbody>

                                </table>

                                <nav aria-label="Page navigation example ">
                                    <ul class="pagination float-right">
                                        <li class="page-item <?php if ($page <= 1) {
                                                                    echo 'disabled';
                                                                } ?>">
                                            <a class="page-link" href="<?php if ($page <= 1) {
                                                                            echo '#';
                                                                        } else {
                                                                            echo "?halaman=" . $prev;
                                                                        } ?>">Previous</a>
                                        </li>

                                        <?php for ($i = 1; $i <= $pages; $i++) : ?>
                                            <li class="page-item <?php if ($page == $i) {
                                                                        echo 'active';
                                                                    } ?>">
                                                <a class="page-link" href="?halaman=<?= $i; ?>"> <?= $i; ?> </a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php if ($page >= $pages) {
                                                                    echo 'disabled';
                                                                } ?>">
                                            <a class="page-link" href="<?php if ($page >= $pages) {
                                                                            echo '#';
                                                                        } else {
                                                                            echo "?halaman=" . $next;
                                                                        } ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>


                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $('.alert').alert()
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mediaelement@4.2.7/build/mediaelementplayer.min.css">