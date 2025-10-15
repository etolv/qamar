<?php

namespace App\Jobs;

use App\Enums\ModelLogEnum;
use App\Models\ModelRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessModelObserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $model, protected $type, protected $user_id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ModelRecord::create([
            'user_id' => $this->user_id,
            'model_type' => get_class($this->model),
            'model_id' => $this->model->id,
            'type' => ModelLogEnum::fromName(strtoupper($this->type))->value
        ]);
    }
}
