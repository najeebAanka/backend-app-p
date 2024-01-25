  <!-- ======= Sidebar ======= -->
  <?php $currentUser = Auth::user(); ?>
  <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">
          <li class="nav-item">
              <a class="nav-link " href="https://test.com">
                  <i class="bi bi-globe"></i>
                  <span>Bidders Website</span>
              </a>
          </li><!-- End Dashboard Nav -->

          <li class="nav-item">
              <a class="nav-link " href="{{ url('') }}">
                  <i class="bi bi-grid"></i>
                  <span>Dashboard</span>
              </a>
          </li><!-- End Dashboard Nav -->



          <!--      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li> End Profile Page Nav -->


          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ url('auctions') }}">
                  <span class="indi"><?= App\Models\Auction::count() ?></span>
                  <i class="fa fa-gavel"></i>

                  <span>Auctions</span>
              </a>
          </li>






          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ url('horses') }}">
                  <span class="indi"><?= App\Models\Horse::where('status', '<>', 'generated')->count() ?></span>
                  <i class="fa fa-horse-head"></i>
                  <span>Horses </span>
              </a>
          </li>





          @if ($currentUser->can('edit-blog-posts'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('news') }}">
                      <span class="indi"><?= App\Models\Blog::count() ?></span>
                      <i class="fa fa-globe"></i>
                      <span>Blog & news </span>
                  </a>
              </li>
          @endif

          @if ($currentUser->can('edit-users'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('users') }}">
                      <span class="indi"><?= App\Models\User::count() ?></span>
                      <i class="fa fa-users"></i>
                      <span>Users</span>
                  </a>
              </li>
          @endif
          @if ($currentUser->can('send-fcm-messages'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('firebase-messages') }}">
                      <span class="indi"><?= App\Models\Notification::count() ?></span>
                      <i class="fa fa-envelope"></i>
                      <span>FCM messages</span>
                  </a>
              </li>
          @endif




          @if ($currentUser->can('edit-static-content'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('static-contents') }}">
                      <i class="fa fa-book"></i>
                      <span>Static contents</span>
                  </a>
              </li>
          @endif


          @if ($currentUser->can('edit-banner'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('banners') }}">
                      <i class="fa fa-image"></i>
                      <span>Banner Management</span>
                  </a>
              </li>
          @endif


          @if ($currentUser->can('modify-roles-permissions'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('roles-and-permissions') }}">
                      <i class="fa fa-shield"></i>
                      <span>Roles and permissions</span>
                  </a>
              </li>
          @endif

          @if ($currentUser->can('view-invoices'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('all-invoices') }}">
                      <i class="fa fa-book"></i>
                      <span>Invoices</span>
                  </a>
              </li>
          @endif

          @if ($currentUser->can('view-invoices'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('recharge-records') }}">
                      <i class="fa fa-wallet"></i>
                      <span>Recharge records</span>
                  </a>
              </li>
          @endif


          @if ((float) $currentUser->wallet_amount > 0)
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('wallet') }}">
                      <i class="fa fa-money-bill"></i>
                      <span>Seller wallet</span>
                  </a>
              </li>
          @endif


          @if ($currentUser->can('view-tracking-log'))
              <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ url('tracking-records') }}">
                      <i class="fa fa-history"></i>
                      <span>Users log</span>
                  </a>
              </li>
          @endif


      </ul>

  </aside><!-- End Sidebar-->
