<?php
session_start();
if(!isset($_SESSION["id"])){
    header("Location:login.php");
}

require("include/core/connection.php");
require("include/core/function.php");

$query = "SELECT * FROM teams WHERE owner = '".$_SESSION["id"]."' ORDER BY id ASC";
$teams = mysqli_query($mysql, $query);

$query = "SELECT * FROM users WHERE team_id = 0 AND level = 'Player' ORDER BY id ASC";
$rest_plyaers = mysqli_query($mysql, $query);

$teamList = array();
$restPlayers = array();
$selectedTeam = $_GET['team'];
$selectedPlayer = $_GET['player'];

if ($selectedTeam) {
    array_push($teamList, $selectedTeam);
} else {
    $data = mysqli_fetch_assoc($teams);
    array_push($teamList, $data['name']);
    $selectedTeam = $data['name'];
}

while ($data = mysqli_fetch_assoc($teams)) {
    if ($data['name'] != $selectedTeam) {
        array_push($teamList, $data['name']);
    }
}

$query = "SELECT * FROM users  WHERE team_id = (SELECT id FROM teams  WHERE name = '".$selectedTeam."')";
$players = mysqli_query($mysql, $query);

if ($selectedPlayer) {
    array_push($restPlayers, $selectedPlayer);
} else {
    $data = mysqli_fetch_assoc($rest_plyaers);
    array_push($restPlayers, $data['name']);
    $selectedPlayer = $data['name'];
}

while ($data = mysqli_fetch_assoc($rest_plyaers)) {
    if ($data['name'] != $selectedPlayer) {
        array_push($restPlayers, $data['name']);
    }
}

$query = "SELECT * FROM users  WHERE name = '".$selectedPlayer."'";
$player_info = mysqli_query($mysql, $query);
$player_profile = mysqli_fetch_assoc($player_info);

?>

<html lang="en">
    <head>
        <?php include("include/view/css.php"); ?>
        <title>Sports League - Team Edit</title>
        <!-- Custom styles for this page -->
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    </head>
    <body id="page-top">
        <!-- Page Wrapper -->
        <div id="wrapper">
            <!-- Sidebar -->
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-laugh-wink"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">Sports League</div>
                </a>
                <!-- Divider -->
                <hr class="sidebar-divider my-0">
                <!-- Nav Item - Dashboard -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">
                <!-- Heading -->
                <div class="sidebar-heading">
                    League
                </div>
                <!-- Nav Item - Dashboard -->
                <li class="nav-item">
                    <a class="nav-link" href="league_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
                <!-- Nav Item - Create League -->
                <li class="nav-item">
                    <a class="nav-link" href="league_create.php">
                    <i class="fas fa-fw fa-crown"></i>
                    <span>Create League</span></a>
                </li>
                <!-- Nav Item - League List -->
                <li class="nav-item">
                    <a class="nav-link" href="league_list.php">
                    <i class="fas fa-fw fa-wind"></i>
                    <span>League List</span></a>
                </li>
                <!-- Nav Item - Invite Team -->
                <li class="nav-item">
                    <a class="nav-link" href="league_invite.php">
                    <i class="fas fa-fw fa-dice-d20"></i>
                    <span>Invite Team</span></a>
                </li>
                <!-- Nav Item - Game Info -->
                <li class="nav-item">
                    <a class="nav-link" href="league_game_info.php">
                    <i class="fas fa-fw fa-star"></i>
                    <span>Game Info</span></a>
                </li>
                <!-- Nav Item - News -->
                <li class="nav-item">
                    <a class="nav-link" href="league_news.php">
                    <i class="fas fa-fw fa-scroll"></i>
                    <span>News</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">
                <!-- Heading -->
                <div class="sidebar-heading">
                    Team
                </div>
                <!-- Nav Item - Create team -->
                <li class="nav-item">
                    <a class="nav-link" href="team_create.php">
                    <i class="fas fa-fw fa-campground"></i>
                    <span>Create Team</span></a>
                <!-- Nav Item - Team Edit -->
                <li class="nav-item active">
                    <a class="nav-link" href="team_edit.php">
                    <i class="fas fa-fw fa-pen-fancy"></i>
                    <span>Team Edit</span></a>
                </li>
                <!-- Nav Item - Team List -->
                <li class="nav-item">
                    <a class="nav-link" href="team_list.php">
                    <i class="fas fa-fw fa-wind"></i>
                    <span>Team List</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">
                <!-- Heading -->
                <div class="sidebar-heading">
                    Profile
                </div>                
                <!-- Nav Item - Profile -->
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                    <i class="fas fa-fw fa-smile"></i>
                    <span>Profile</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">
                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            <!-- End of Sidebar -->
            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Main Content -->
                <div id="content">
                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                        <!-- Sidebar Toggle (Topbar) -->
                        <?php include("include/view/topbar.php"); ?>
                    </nav>
                    <!-- End of Topbar -->
                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        <!-- Page Heading -->
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-4 text-gray-800">Team Edit</h1>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-7">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">Select Team</h6>
                                        <select id="league" name="league" class="form-control w-50" onchange="changeTeam(this.value)" required>
                                            <?php foreach ($teamList as $i => $team) { ?>
                                                <option><?php echo $team; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-xl-4 col-lg-7">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">Add Player</h6>
                                    </div>
                                    <div class="card-body">
                                        <form class="user" method="post">
                                            <div class="form-group">
                                                <label>Select Player</label>
                                                <select id="league" name="league" class="form-control" onchange="changePlayer(this.value)" required>
                                                    restPlayers
                                                    <?php foreach ($restPlayers as $i => $player) { ?>
                                                        <option><?php echo $player; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Avatar</label>
                                                <div class="form-group text-center">
                                                    <img class="logo-image rounded-circle" src="<?php echo $player_profile['avatar'];?>">
                                                </div>
                                            </div>
                                            <br>
                                            <button type="submit" id="invite" name="invite" class="btn btn-primary btn-user btn-block">Add</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-7">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">Player List</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th hidden>Id</th>
                                                        <th>Name</th>
                                                        <th>Avatar</th>
                                                        <th>Email</th>
                                                        <th>Contact</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th hidden>Id</th>
                                                        <th>Name</th>
                                                        <th>Avatar</th>
                                                        <th>Email</th>
                                                        <th>Contact</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php $i = 0; while($player = mysqli_fetch_array($players)) { ?>
                                                        <tr>
                                                            <td class="align-middle" hidden><?php echo $player['id']; ?></td>
                                                            <td class="align-middle"><?php echo $player['name']; ?></td>
                                                            <td class="align-middle text-center">
                                                                <img class="logo-image rounded-circle" src="<?php echo $player['avatar'];?>">
                                                            </td>
                                                            <td class="align-middle"><?php echo $player['email']; ?></td>
                                                            <td class="align-middle"><?php echo $player['contact']; ?></td>
                                                            <td class="align-middle text-center">
                                                                <a href="#deleteModal" class="btn btn-danger btn-circle btn-sm" data-toggle="modal"  data-target="#deleteModal" data-value="<?php echo $player['id'];?>"><i class="fas fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php $i++; } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->
                <!-- Footer -->
                <?php include("include/view/footer.php"); ?>
                <!-- End of Footer -->
            </div>
            <!-- End of Content Wrapper -->
        </div>
        <!-- End of Page Wrapper -->
        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
        </a>
        <!-- Logout Modal-->
        <?php include("include/modal/logout_modal.php"); ?>
        <!-- League Delete Modal-->
        <?php include("include/modal/player_delete_modal.php"); ?>
        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>        
        <!-- Page level plugins -->
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>
        <!-- Custom scripts for this pages-->
        <script src="js/app/team_edit.js"></script>
    </body>
</html>