<?php
namespace Core\Toaster;

class Toast {

    public function success(string $message): string
    {
        return "<div class='alert alert-success'>
                    $message
                </div>";
    }

    public function error(string $message): string
    {
        return "<div class='alert alert-danger'>
                    $message
                </div>";
    }

    public function warning(string $message): string
    {
        return "<div class='alert alert-warning'>
                    $message
                </div>";
    }
}