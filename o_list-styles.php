<?php
echo '
<style>
    body {
        font-family: "Roboto", sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
    }

    .sidebar {
        background-color: #343a40;
        color: white;
        width: 80px;
        height: 100vh;
        transition: width 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        overflow-y: auto;
    }

    .sidebar.expanded {
        width: 250px;
    }

    .sidebar .top-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .sidebar .logo {
        font-size: 1.5rem;
        padding: 20px 0;
        text-align: center;
        width: 100%;
    }

    .sidebar .profile {
        text-align: center;
        padding: 10px 0;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar .profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid black;
    }

    .sidebar .profile .name {
        margin-left: 10px;
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .sidebar .separator {
        width: 100%;
        height: 1px;
        background-color: white;
        margin: 10px 0;
    }

    .sidebar .menu {
        list-style: none;
        padding: 0;
        width: 100%;
        flex-grow: 1;
    }

    .sidebar .menu li {
        width: 100%;
    }

    .sidebar .menu li a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 15px;
        transition: background 0.3s;
        justify-content: center;
        white-space: nowrap;
        overflow: hidden;
        position: relative;
    }

    .sidebar.expanded .menu li a {
        justify-content: flex-start;
    }

    .sidebar .menu li a .text {
        display: none;
    }

    .sidebar.expanded .menu li a .text {
        display: inline;
        margin-left: 10px;
    }

    .sidebar .menu li a:hover {
        background-color: #DE2910;
    }

    .sidebar .nav-item .nav-submenu {
        display: none;
        list-style: none;
        padding: 0;
    }

    .sidebar .nav-item .nav-submenu li a {
        padding-left: 30px;
        font-size: 0.75rem;
        padding: 10px;
    }

    .sidebar .nav-item.active .nav-submenu {
        display: block;
    }

    .sidebar .nav-item .nav-submenu li a.active {
        background-color: #8B0000;
        color: white;
    }

    .sidebar .menu li a .submenu-arrow {
        position: absolute;
        right: 15px;
        transition: transform 0.3s;
    }

    .sidebar .menu li a .submenu-arrow.rotated {
        transform: rotate(90deg);
    }

    .content {
        flex-grow: 1;
        padding: 20px;
    }

    .toggle-btn {
        position: fixed;
        top: 15px;
        left: 85px;
        cursor: pointer;
        font-size: 1.5rem;
        color: #343a40;
        transition: left 0.3s;
    }

    .sidebar.expanded+.content .toggle-btn {
        left: 255px;
    }

    .sidebar img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid black;
    }

    .logout {
        margin-bottom: 20px;
        width: 100%;
    }

    .logout a {
        color: white;
        text-decoration: none;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 15px;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar .logout a .text {
        display: none;
    }

    .sidebar.expanded .logout a .text {
        display: inline;
        margin-left: 10px;
    }

    .logout a:hover {
        text-decoration: underline;
    }

    .profile .name {
        display: inline;
    }

    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.8);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
    }

    .status-pending {
        background-color: red;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: center;
    }

    .status-completed {
        background-color: green;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: center;
    }

    .status-incomplete {
        background-color: yellow;
        color: black;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: center;
    }

    .update-modal .status-select {
        width: 100px;
    }
</style>
';
?>
