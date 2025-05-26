<?php
// header.php


$title = isset($title) ? $title : 'Dashboard'; // Default title if not set
$username = $_SESSION['user']['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        .header { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.05); z-index: 10; }
        .header-container { padding: 1rem; display: flex; align-items: center; justify-content: space-between; }
        .header-left, .header-right { display: flex; align-items: center; }
        .menu-button { margin-right: 1rem; color: #6b7280; background: none; border: none; cursor: pointer; }
        .header-title { font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0; }
        .header-right { gap: 1rem; }
        .notification-button { color: #6b7280; background: none; border: none; position: relative; cursor: pointer; }
        .notification-button:hover { color: #374151; }
        .notification-dot { position: absolute; top: 0; right: 0; height: .5rem; width: .5rem; border-radius: 9999px; background: #ef4444; }
        .profile-button { display: flex; align-items: center; gap: .5rem; background: none; border: none; cursor: pointer; position: relative; }
        .profile-icon { background: #e5e7eb; padding: .5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; }
        .profile-icon svg { color: #4b5563; }
        .profile-name { display: none; font-size: .875rem; font-weight: 500; color: #374151; }
        @media (min-width:768px) { .profile-name { display: inline-block; } }
        .dropdown { position: absolute; right: 0; margin-top: .5rem; width: 12rem; background: #fff; border-radius: .375rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -2px rgba(0,0,0,.05); padding: .25rem 0; z-index: 10; }
        .dropdown.hidden { display: none; }
        .dropdown-item { display: flex; align-items: center; width: 100%; text-align: left; padding: .5rem 1rem; font-size: .875rem; color: #374151; background: none; border: none; cursor: pointer; }
        .dropdown-item:hover { background: #f3f4f6; }
        .dropdown-icon { margin-right: .5rem; }
        .relative { position: relative; }
        .lg\:hidden { display: block; }
        @media (min-width:1024px) { .lg\:hidden { display: none; } }
        /* Utility classes for flex and gap if not using a framework */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-1 { gap: .25rem; }
        .gap-2 { gap: .5rem; }
      
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <button class="menu-button lg:hidden" onclick="toggleMobileSidebar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <h1 class="header-title"><?php echo htmlspecialchars($title); ?></h1>
            </div>
            <div class="header-right">
                <div class="relative">
                    <button class="notification-button hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-dot"></span>
                    </button>
                </div>
                <div class="relative">
                    <button class="profile-button" onclick="toggleDropdown()">
                        <div class="profile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <span class="profile-name"><?php echo htmlspecialchars($username); ?></span>
                    </button>
                    <div id="dropdown" class="dropdown hidden">
                        <form method="POST" action="logout.php">
                            <button type="submit" class="dropdown-item hover:bg-gray-100 flex items-center" style="background:none;border:none;width:100%;text-align:left;cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dropdown-icon">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }

        function toggleMobileSidebar() {
            // You'll need to implement this based on your sidebar implementation
            console.log('Toggle mobile sidebar');
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const profileButton = document.querySelector('.profile-button');
            
            if (!profileButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
            }
        
    </script>
</body>
</html>