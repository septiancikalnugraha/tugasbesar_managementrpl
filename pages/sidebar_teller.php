<?php
// Sidebar Teller
?>
<div class="sidebar-menu-btns" style="display:flex;flex-direction:column;gap:1rem;margin-top:2rem;">
    <a href="dashboard_petugas.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='dashboard_petugas.php'?' active':'' ?>"><i class="fa fa-home"></i> Dashboard</a>
    <a href="teller_nasabah.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='teller_nasabah.php'?' active':'' ?>"><i class="fa fa-users"></i> Nasabah</a>
    <a href="teller_transaksi.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='teller_transaksi.php'?' active':'' ?>"><i class="fa fa-exchange-alt"></i> Transaksi</a>
    <a href="teller_pengaturan.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='teller_pengaturan.php'?' active':'' ?>"><i class="fa fa-cog"></i> Pengaturan</a>
    <a href="logout.php" class="sidebar-btn sidebar-logout" style="margin-top:2rem;"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div> 