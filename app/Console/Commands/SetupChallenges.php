<?php

namespace App\Console\Commands;

use DB;
use Settings;
use Illuminate\Console\Command;

use App\Models\User\User;
use App\Models\Prompt\Prompt;
use App\Services\PromptService;

class SetupChallenges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup-challenges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs setup for the Challenges extension.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('********************');
        $this->info('* SETUP CHALLENGES *');
        $this->info('********************'."\n");

        if(!DB::table('site_settings')->where('key', 'challenges_prompt')->exists()) {
            $this->info('This will create a new prompt for users to submit challenge entries to. Once created, you can modify this prompt as desired.');
            $this->info('It will also create a site setting to store this prompt\'s ID in and set it to the created prompts\'.');

            $data = [
                'prompt_category_id' => null,
                'name' => 'Challenge Submissions',
                'summary' => 'Submit challenge logs here!',
                'hide_submissions' => 0,
                'is_active' => 1
            ];

            $promptService = $service = new PromptService;

            if(!$prompt = $promptService->createPrompt($data, User::find(Settings::get('admin_user')))) {
                foreach($service->errors()->getMessages()['error'] as $error) $this->error($error);
            }
            else $this->info("Added:   Challenge Submission Prompt");
        }
        else {
            $this->line('There is already a prompt ID set. Please modify this via site settings.');
            $this->line('Skipping...');
        }

        $this->line('Adding site settings...');

        if(isset($prompt) && $prompt) {
            if(!DB::table('site_settings')->where('key', 'challenges_prompt')->exists()) {
                DB::table('site_settings')->insert([
                    [
                        'key' => 'challenges_prompt',
                        'value' => $prompt->id,
                        'description' => 'ID of the prompt to use for challenge submissions.'
                    ]

                ]);
                $this->info("Added:   challenges_prompt");
            }
            else $this->line("Skipped: challenges_prompt");
        }

        if(!DB::table('site_settings')->where('key', 'challenges_concurrent')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'challenges_concurrent',
                    'value' => 1,
                    'description' => 'Number of current challenges a user can have running at once. Note that setting this to 0 will disable registration, but not cancel any currently running challenges.'
                ]

            ]);
            $this->info("Added:   challenges_concurrent");
        }
        else $this->line("Skipped: challenges_concurrent");
    }
}
