<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Notif;
use App\Models\Question;

use Illuminate\Support\Str;
use App\Mail\VerificationEmail;
use App\Mail\NewPassword;
use App\Mail\WelcomeMail;
use App\Mail\RdvEmail;
use App\Mail\MessageEmail;
use App\Mail\Form1Mail;
use App\Mail\MailtrapExample;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Storage;
use PDF;

use Carbon\Carbon;

class EmailsController extends Controller
{










}
