<?php

namespace Spatie\Health\Checks;

use Exception;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Spatie\Health\Checks\Check;
use Spatie\Health\Support\Result;

class HorizonCheck extends Check
{
    public function run(): Result
    {
        $result = Result::make();

        try {
            $horizon = app(MasterSupervisorRepository::class);
        } catch (Exception $exception) {
            return $result->failed('Horizon does not seem to be installed correctly.');
        }

        $masterSupervisors = $horizon->all();

        if (count($masterSupervisors) === 0)
        {
            return $result->failed("Horizon is not running.");
        }

        $masterSupervisor = $masterSupervisors[0];

        if ($masterSupervisor->status === 'paused') {
            return $result->warning('Horizon is running, but the status is paused.');
        }

        return $result->ok();
    }
}