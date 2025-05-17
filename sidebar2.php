<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> -->
    <style>
        /* Sidebar Styles */
.sidebar {
    background-color: #1e1b4e; 
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    padding: 20px 0;
    width: 250px;
    position: fixed;
    height: 100vh;
    left: 0;
    top: 0;
}

@media (max-width: 768px){
    .sidebar{
        width: 100%;
        position: relative;
        height: auto;
    }
}

.logo {
    text-align: center;
    margin-bottom: 30px;
    padding: 0 20px;
    color:#ffffff;
}

.logo img {
    max-width: 150px;
}

.nav-menu {
    list-style: none;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.3s;
}

.nav-link:hover, .nav-link.active {
    background-color: #3a3f5f;
    color: white;
}

.nav-link i {
    margin-right: 10px;
    font-size: 18px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .sidebar {
        display: none;
    }
}
    </style>
</head>   
<body>
    <!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="logo">
        <img src="images/Qoricha logo.png" alt="JO TECH Logo">
    </div>
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="#" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-boxes"></i>
                User Management
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                Stock Management
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                Categories
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-users-cog"></i>
                Reports
            </a>
        </li>
    </ul>
</aside>
</body>
