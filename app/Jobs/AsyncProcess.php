<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SuperClosure\Serializer;
use Closure;

class AsyncProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $handle;

    protected $serializer;

    /**
     * Create a new job instance.
     *
     * @param Closure $handle
     */
    public function __construct(Closure $handle)
    {
        $this->serializer = new Serializer;
        $this->handle = $this->serializer->serialize($handle);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->serializer->unserialize($this->handle)();
    }

    public static function make(Closure $handle)
    {
        dispatch(new self($handle));
    }
}
