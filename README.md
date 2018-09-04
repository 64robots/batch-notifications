# Batch Notifications

## Description

Batch Notifications is a Laravel package that groups repetitive notifications in batches.
This package is intended for cases where notifications are dispatched repeatedly for a same Notifiable model.
So, instead of sending lots of notifications (ex.: email messages) repeatedly to the Notifiable model, the
notifications are grouped in batches that will be sent in periods of time.

## Installation

#### 1 - Require the package

``
composer require 64robots/batch-notifications
``

#### 2 - Publish

``
php artisan vendor:publish --provider="R64\BatchNotifications\BatchNotificationsServiceProvider"
``

#### 3 - Run the migration that was just published

``
php artisan migrate
``

## Usage

#### 1 - 