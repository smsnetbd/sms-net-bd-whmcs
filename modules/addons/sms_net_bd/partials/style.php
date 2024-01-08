<style>
    .custom-container {
        margin: 0 auto;
        width: 100%;
        padding: 0 25px;
        max-width: 1100px;
    }

    .sms_net_bd_wrapper .tabbed_section {
        max-width: 100%;
        min-height: 300px;
        border: 1px solid #2f353b;
    }

    .sms_net_bd_wrapper .tabbed_section_nav_wrap {
        list-style: none;
        display: flex;
        background-color: #44b6ae;
        color: #ffffff;
        padding: 0px;
    }


    .sms_net_bd_wrapper .tabbed_section_nav {
        flex: 1;
        text-align: center;
        cursor: pointer;
    }

    .sms_net_bd_wrapper .tabbed_section_nav a {
        color: #ffffff;
        display: block;
        padding: 10px 10px;
        background-color: #2f353b;
        text-decoration: none;
    }

    .sms_net_bd_wrapper .tabbed_section_nav a:hover {
        background-color: #44b6ae;
        color: #ffffff;
    }

    .sms_net_bd_wrapper .tabbed_section_nav_active a {
        background-color: #44b6ae;
        color: #ffffff;
    }

    .sms_net_bd_wrapper .tabbed_section_content_wrap {
        padding: 10px;
    }

    .sms_net_bd_wrapper .tabbed_section_content {
        display: none;
    }

    .sms_net_bd_wrapper .tabbed_section_content_active {
        display: block;
    }

    .fieldlabel input[type="checkbox"] {
        margin-right: 5px;
    }

    .logo {
        padding: 10px;
        height: 120px;
        display: flex;
        justify-content: center;
        border-radius: 5px;
        flex-direction: column;
        align-items: center;
        border: 1px solid #cacaca;
    }

    .sms_net_bd_wrapper .btn-primary {
        color: #fff;
        background-color: #44b6ae;
        border-color: #44b6ae;
    }

    .social-links {
        display: flex;
        justify-content: center;
        margin-top: 10px;
        gap: 10px;
    }

    .sms_net_bd_wrapper .error {
        text-align: center;
        padding: 20px;
        border: 1px solid #cacaca;
        border-radius: 5px;
        margin: 20px 0px;
    }

    .social-links a {
        color: #2f353b;
        margin: 0px 2px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        font-size: 24px;
        text-decoration: none;
    }

    .social-links a span {
        font-size: 12px;
    }

    /* billow 600 px screen */
    @media only screen and (max-width: 1010px) {
        .sms_net_bd_wrapper ul li a span {
            display: none;
        }

        hr {
            margin-top: 0px !important;
            margin-bottom: 0px !important;
        }
    }
</style>