<?php
namespace Tests\Unit\Mail;

use App\Mail\CredentialsMail;
use App\Models\User;
use Tests\TestCase;

class CredentialsMailTest extends TestCase
{
    public function test_mail_contains_credentials(): void
    {
        $user = User::factory()->make(['name' => 'Hans Schmidt', 'email' => 'hans@example.com']);
        $mail = new CredentialsMail($user, 'testpassword123', 'http://localhost/login');
        $mail->assertSeeInHtml('Hans Schmidt');
        $mail->assertSeeInHtml('hans@example.com');
        $mail->assertSeeInHtml('testpassword123');
        $mail->assertSeeInHtml('http://localhost/login');
    }
}
