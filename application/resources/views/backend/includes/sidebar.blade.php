<div class="col-md-3 left_col menu_fixed">
	<div class="left_col scroll-view">
		<div class="navbar nav_title" style="border: 0;">
			<a href="{{ route('admin.home') }}" class="site_title"> <span>HRIS EDigindo</span></a>
		</div>

		<div class="clearfix"></div>

		<div class="profile">
			<div class="profile_pic">
				<img src="{{ asset(Auth::user()->avatar != '' ? Auth::user()->avatar : 'backend/images/user.png') }}" alt="..." class="img-circle profile_img">
			</div>
			<div class="profile_info">
				<span>Hai,</span>
				<h2>{{ Auth::user()->name }}</h2>
			</div>
		</div>

		<br />

		<!-- sidebar menu -->
		<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			<div class="menu_section">
				<h3>General</h3>
				<ul class="nav side-menu">
					<li><a href="{{ route('admin.home') }}"><i class="fa fa-home"></i>Beranda</a></li>
					<li>
						<a>
							<i class="fa fa-beer"></i>Data Karyawan<span class="fa fa-chevron-down"></span>
						</a>
						<ul class="nav child_menu" style="">
							<li ><a href="{{ route('admin.employee') }}">Data Karyawan</a></li>
							<li ><a href="{{ route('admin.jobTitle') }}">Nama Pekerjaan</a></li>
							<li ><a href="{{ route('admin.itemBorrowed') }}">Peminjaman Barang</a></li>
						</ul>
					</li>
					<li>
						<a>
							<i class="fa fa-beer"></i>Data Izin<span class="fa fa-chevron-down"></span>
						</a>
						<ul class="nav child_menu" style="">
							<li ><a href="{{ route('admin.leave') }}">Meninggalkan Kantor</a></li>
							<li ><a href="{{ route('admin.dayoff') }}">Cuti</a></li>
							<li ><a href="{{ route('admin.holiday') }}">Libur</a></li>
							<li ><a href="{{ route('admin.calender') }}">Kalender</a></li>
						</ul>
					</li>
					<li>
						<a>
							<i class="fa fa-beer"></i>Data Absensi<span class="fa fa-chevron-down"></span>
						</a>
						<ul class="nav child_menu" style="">
							<li ><a href="{{ route('admin.shift') }}">Shift</a></li>
							<li ><a href="{{ route('admin.attendance') }}">Jadwal</a></li>
							<li ><a href="{{ route('admin.absence') }}">Laporan Absen</a></li>
							<li ><a href="{{ route('admin.overtime') }}">Lembur</a></li>
						</ul>
					</li>
					<li>
						<a href="{{ route('admin.employeeContract') }}"><i class="fa fa-calendar"></i>Data Kontrak</a>
					</li>
					<li>
						<a href="{{ route('admin.employeePayroll') }}"><i class="fa fa-calendar"></i>Data Gaji</a>
					</li>
					<li>
						<a href="{{ route('admin.user') }}"><i class="fa fa-users"></i>Data User</a>
					</li>
					
				</ul>

			</div>

		</div>
		<!-- /sidebar menu -->

		<!-- /menu footer buttons -->
		<div class="sidebar-footer hidden-small">
			<a href="{{ route('admin.user') }}" data-toggle="tooltip" data-placement="top" title="Users">
				<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			</a>
			<a href="#" data-toggle="tooltip" data-placement="top" title="Inbox">
				<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
			</a>
			<a href="{{ route('admin.user.edit', ['id' => Auth::id()]) }}" data-toggle="tooltip" data-placement="top" title="Profile">
				<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
			</a>
			<a href="{{ route('admin.logout') }}" data-toggle="tooltip" data-placement="top" title="Logout">
				<span class="glyphicon glyphicon-off" aria-hidden="true"></span>
			</a>
		</div>
		<!-- /menu footer buttons -->
	</div>
</div>
