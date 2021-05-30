<?php

namespace Hexadog\TranslationManager\Console\Commands;

use Illuminate\Console\Command;

class MissingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:missing {--namespace=*} {--l|lang=*} {--f|fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all missing translations';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Lang', 'Namespace', 'String'];

    /**
     * Prompt for module's alias name.
     */
    public function handle()
    {
        $result = [];

        $strings = \TranslationManager::findMissing($this->option('namespace'), $this->option('lang'), boolval($this->option('fix')));
        $total = 0;

        foreach ($strings as $lang => $namespaces) {
            foreach ($namespaces as $namespace => $strings) {
                foreach ($strings as $string) {
                    $result[] = [
                        'lang' => $lang,
                        'namespace' => $namespace,
                        'string' => $string,
                    ];

                    ++$total;
                }
            }
        }

        if ($total) {
            $this->error(sprintf('%d missing translation found', $total));
            $this->table($this->headers, $result);
        } else {
            $this->comment('No missing translation');
        }
    }
}
