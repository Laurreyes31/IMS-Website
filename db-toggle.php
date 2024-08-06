<!-- db-toggle.php -->
<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const productMenuToggle = document.querySelector('.product-menu-toggle');
    const supplierMenuToggle = document.querySelector('.supplier-menu-toggle');
    const purchaseOrderMenuToggle = document.querySelector('.purchase-order-menu-toggle');

    console.log('Sidebar:', sidebar);
    console.log('Toggle Button:', toggleBtn);
    console.log('User Menu Toggle:', userMenuToggle);
    console.log('Product Menu Toggle:', productMenuToggle);
    console.log('Supplier Menu Toggle:', supplierMenuToggle);
    console.log('Purchase Order Menu Toggle:', purchaseOrderMenuToggle);

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('expanded');
        document.querySelector('.content').classList.toggle('expanded');
        console.log('Toggle button clicked');

        if (!sidebar.classList.contains('expanded')) {
            closeAllSubmenus();
        }
    });

    function closeAllSubmenus() {
        const submenus = document.querySelectorAll('.nav-submenu');
        const arrows = document.querySelectorAll('.submenu-arrow');
        submenus.forEach(submenu => submenu.style.display = 'none');
        arrows.forEach(arrow => arrow.classList.remove('rotated'));
    }

    userMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const submenu = userMenuToggle.parentElement.querySelector('.nav-submenu');
        const arrow = userMenuToggle.querySelector('.submenu-arrow');
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        arrow.classList.toggle('rotated');
        console.log('User menu toggle clicked');
    });

    productMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const submenu = productMenuToggle.parentElement.querySelector('.nav-submenu');
        const arrow = productMenuToggle.querySelector('.submenu-arrow');
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        arrow.classList.toggle('rotated');
        console.log('Product menu toggle clicked');
    });

    supplierMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const submenu = supplierMenuToggle.parentElement.querySelector('.nav-submenu');
        const arrow = supplierMenuToggle.querySelector('.submenu-arrow');
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        arrow.classList.toggle('rotated');
        console.log('Supplier menu toggle clicked');
    });

    purchaseOrderMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const submenu = purchaseOrderMenuToggle.parentElement.querySelector('.nav-submenu');
        const arrow = purchaseOrderMenuToggle.querySelector('.submenu-arrow');
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        arrow.classList.toggle('rotated');
        console.log('Purchase Order menu toggle clicked');
    });
</script>
