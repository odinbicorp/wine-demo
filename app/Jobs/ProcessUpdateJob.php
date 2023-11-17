<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $start;
    protected $end;

    /**
     * Create a new job instance.
     *
     * @param int $start
     * @param int $end
     */
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Fetch wines between the specified range and update them
            $wines = Wine::getWineBetween($this->start, $this->end);

            // Your updating logic here
            foreach ($wines as $wine) {
                // Update the wine
                // Example: $wine->update(['attribute' => 'new_value']);
            }
        } catch (\Exception $e) {
            // Handle exceptions if necessary
            // You might want to log the exception or dispatch another job for retries
            \Log::error('Error updating wines: ' . $e->getMessage());
        }
    }
}
