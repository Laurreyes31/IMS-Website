<!-- db-sidebar.php -->
<div class="sidebar" id="sidebar">
    <div class="top-section">
        <div class="logo">
            <img src="icons/logo.jpg" alt="Logo">
        </div>
        <div class="profile">
            <img src="icons/dashboardprofile.jpg" alt="Profile Picture">
            <div class="name">
                <span class="full-name"><?php echo $first_name . ' ' . $last_name; ?></span>
                <span class="initials" style="display:none;"><?php echo $initials; ?></span>
            </div>
        </div>
        <div class="separator"></div>
    </div>
    <ul class="menu">
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="text">Dashboard</span></a></li>
        <li><a href="reports.php"><i class="fas fa-file-alt"></i><span class="text">Reports</span></a></li>
        <li class="nav-item">
            <a href="#" class="product-menu-toggle"><i class="fas fa-tag"></i><span class="text">Product</span><i class="submenu-arrow fas fa-chevron-left"></i></a>
            <ul class="nav-submenu">
                <li><a href="view_products.php">View Products</a></li>
                <li><a href="add_product.php">Add Product</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="#" class="supplier-menu-toggle"><i class="fas fa-truck"></i><span class="text">Supplier</span><i class="submenu-arrow fas fa-chevron-left"></i></a>
            <ul class="nav-submenu">
                <li><a href="view_suppliers.php">View Suppliers</a></li>
                <li><a href="add_supplier.php">Add Supplier</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a href="#" class="purchase-order-menu-toggle"><i class="fas fa-shopping-cart"></i><span class="text">Purchase Order</span><i class="submenu-arrow fas fa-chevron-left"></i></a>
            <ul class="nav-submenu">
                <li><a href="create_order.php">Create Order</a></li>
                <li><a href="view_orders.php">View Orders</a></li>
            </ul>
        </li>        

        <li class="nav-item">
            <a href="#" class="user-menu-toggle"><i class="fas fa-user"></i><span class="text">User</span><i class="submenu-arrow fas fa-chevron-left"></i></a>
            <ul class="nav-submenu">
                <li><a href="view_users.php">View Users</a></li>
                <li><a href="add_user.php">Add User</a></li>
            </ul>
        </li>
    </ul>
    <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="text">Log Out</span></a>
    </div>
</div>
