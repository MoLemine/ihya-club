<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('blood-requests:archive')->daily();
