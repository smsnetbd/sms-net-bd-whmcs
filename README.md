# WHMCS SMS Notification Module for sms.net.bd

## Overview

The WHMCS SMS Notification Module for **sms.net.bd** is a powerful and versatile solution designed to enhance communication between WHMCS users and their clients through **SMS notifications**. This module seamlessly integrates with WHMCS, providing support for a wide range of hooks to trigger SMS messages, ensuring that important events and actions are promptly communicated.

## Prerequisites

- WHMCS 8.0 or later
- sms.net.bd [account](https://sms.net.bd/signup)

## Installation

- **Download and Extract**
  - Download the module from the [releases](https://github.com/smsnetbd/whmcs-sms-module/releases) page and extract the contents of the archive to a local directory.
  - Upload the `modules` folder to the root directory of your WHMCS installation folder.
- **Configuration**
  - Login to WHMCS admin panel and navigate to `addons > sms.net.bd`
  - Configure the module settings in the WHMCS admin panel providing the SMS `API Key` for **sms.net.bd**.
- **Activate Hooks:** Activate the desired hooks within the WHMCS admin panel to trigger SMS notifications for specific events.

## Features

### Supported WHMCS Hooks

> - **Accept Order:** Notify clients when their orders are accepted.

> - **Admin Login:** Keep administrators informed about successful logins.

> - **After Module Change Package:** Inform clients of any changes to their hosting packages.

> - **After Module Change Password:** Notify clients after a change in their hosting account password.

> - **After Module Create Hosting:** Send messages when a new hosting account is created.

> - **After Module Suspend:** Alert clients when their hosting account is suspended.

> - **After Module Unsuspend:** Notify clients upon the unsuspension of their hosting account.

> - **After Registrar Registration:** Inform clients of successful domain registrations.

> - **After Registrar Registration Failed:** Notify clients if domain registration fails.

> - **After Registrar Registration Failed Admin:** Alert administrators about failed domain registrations.

> - **After Registrar Registration Admin:** Inform administrators of successful domain registrations.

> - **After Registrar Renewal:** Notify clients about successful domain renewals.

> - **After Registrar Renewal Failed Admin:** Alert administrators about failed domain renewal attempts.

> - **After Registrar Renewal Admin:** Inform administrators of successful domain renewals.

> - **Client Add:** Welcome new clients with an introductory SMS message.

> - **Client Add Admin:** Notify administrators when a new client is added.

> - **Client Area Register:** Send SMS confirmations for client registrations.

> - **Client Change Password:** Alert clients about changes to their account passwords.

> - **Client Edit:** Inform clients when their account information is edited.

> - **Client Login Admin:** Notify administrators when clients log in.

> - **Domain Renewal Notice:** Remind clients about upcoming domain renewals.

> - **Invoice Created:** Send instant notifications when invoices are created.

> - **Invoice Paid:** Confirm payment receipt with SMS notifications.

> - **Invoice Payment Reminder First/Reminder/Second/Third:** Send payment reminders at different intervals.

> - **Ticket Admin Reply:** Keep clients informed of admin replies to support tickets.

> - **Ticket Close:** Notify clients when their support tickets are successfully closed.

> - **Ticket Open Admin:** Alert administrators when new support tickets are opened.

> - **Ticket User Reply Admin:** Inform administrators about client replies to support tickets.

## Usage

Once installed and configured, the WHMCS SMS Notification Module for **sms.net.bd** will seamlessly integrate with your WHMCS environment, keeping both clients and administrators informed through timely and personalized **SMS notifications**.

Enhance your communication strategy and improve client satisfaction by leveraging the power of **SMS notifications** with this feature-rich WHMCS module.
