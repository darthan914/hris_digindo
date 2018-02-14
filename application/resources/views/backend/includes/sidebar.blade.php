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

					@if(Auth::user()->can('list-employee') || Auth::user()->can('list-jobTitle'))
					<li>
						<a>
							<i class="fa fa-beer"></i>Data Karyawan<span class="fa fa-chevron-down"></span>
						</a>
						<ul class="nav child_menu" style="">
							@can('list-employee')
							<li ><a href="{{ route('admin.employee') }}">Data Karyawan</a></li>
							@endcan

						</ul>
					</li>
					@endif

					<li>
						<a>
							<i class="fa fa-beer"></i>Data Izin<span class="fa fa-chevron-down"></span>
						</a>
						<ul class="nav child_menu" style="">
							@can('list-leave')
							<li ><a href="{{ route('admin.leave') }}">Meninggalkan Kantor</a></li>
							@endcan

							@can('list-dayoff')
							<li ><a href="{{ route('admin.dayoff') }}">Izin/Cuti/Sakit</a></li>
							@endcan

							@can('list-overtime')
							<li ><a href="{{ route('admin.overtime') }}">Lembur</a></li>
							@endcan
						</ul>
					</li>

					@if(Auth::user()->can('list-shift') || Auth::user()->can('list-attendance') || Auth::user()->can('list-absence') || Auth::user()->can('list-overtime'))
					<li>
						<a>
							<i class="fa fa-beer"></i>Data Absensi<span class="fa fa-chevron-down"></span>
						</a>
						<ul class="nav child_menu" style="">
							@can('list-shift')
							<li ><a href="{{ route('admin.shift') }}">Shift</a></li>
							@endcan

							@can('list-attendance')
							<li ><a href="{{ route('admin.attendance') }}">Jadwal</a></li>
							@endcan

							@can('list-absence')
							<li ><a href="{{ route('admin.absence') }}">Laporan Absen</a></li>
							@endcan

							@can('list-holiday')
							<li ><a href="{{ route('admin.holiday') }}">Libur</a></li>
							@endcan

							
						</ul>
					</li>
					@endif

					<li>
						<a href="{{ route('admin.calender') }}"><i class="fa fa-calendar"></i>Kalender</a>
					</li>

					@can('list-borrow')
					<li>
						<a href="{{ route('admin.borrow') }}"><i class="fa fa-calendar"></i>Peminjaman Barang</a>
					</li>
					@endcan

					@can('list-contract')
					<li>
						<a href="{{ route('admin.employee.contract') }}"><i class="fa fa-calendar"></i>Data Kontrak</a>
					</li>
					@endcan

					@can('list-payroll')
					<li>
						<a href="{{ route('admin.employee.payroll') }}"><i class="fa fa-calendar"></i>Data Gaji</a>
					</li>
					@endcan

				</ul>

			</div>

			<div class="menu_section">
                <h3>Access</h3>
	                <ul class="nav side-menu">
	                	@can('list-user')
	                  	<li class="{{ Route::is('admin.user*') ? 'active' : '' }}"><a href="{{ route('admin.user') }}"><i class="fa fa-users"></i></i>User List</a></li>
	                  	@endcan

	                  	@can('list-role')
	                  	<li class="{{ Route::is('admin.role*') ? 'active' : '' }}"><a href="{{ route('admin.role') }}"><i class="fa fa-home"></i>Role List</a></li>
	                  	@endcan

	                  	@can('list-file')
	                  	<li class="{{ Route::is('admin.file*') ? 'active' : '' }}"><a href="{{ route('admin.file') }}"><i class="fa fa-home"></i>File Management</a></li>
	                  	@endcan

	                  	@can('config')
	                  	<li class="{{ Route::is('admin.config*') ? 'active' : '' }}"><a href="{{ route('admin.config') }}"><i class="fa fa-home"></i>Configuration</a></li>
	                  	@endcan

	                  	@can('sql')
	                  	<li class="{{ Route::is('admin.sql*') ? 'active' : '' }}"><a href="{{ route('admin.sql') }}"><i class="fa fa-home"></i>SQL Database</a></li>
	                  	@endcan
	                  </ul>
            </div>


		</div>

		
		</div>
		<!-- /sidebar menu -->

		<!-- /menu footer buttons -->
		<div class="sidebar-footer hidden-small">
			@can('list-user')
			<a href="{{ route('admin.user') }}" data-toggle="tooltip" data-placement="top" title="Users">
				<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			</a>
			@endcan
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
