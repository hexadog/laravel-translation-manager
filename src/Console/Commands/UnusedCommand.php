<?php

namespace Hexadog\TranslationManager\Console\Commands;

use Illuminate\Console\Command;

class UnusedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:unused {--namespace=*} {--l|lang=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all unused translated strings';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Lang', 'Namespace', 'Key',  'String'];

    /**
     * Prompt for module's alias name
     *
     */
    public function handle()
    {
        $result = [];

        $strings = \TranslationManager::findUnused($this->option('namespace'), $this->option('lang'));

        // Count total strings found
        $total = array_reduce($strings, function ($result, $namespaces) {
            return $result + array_reduce($namespaces, function ($result, $namespace) {
                return $result + count($namespace);
            }, 0);
        }, 0);

        foreach ($strings as $lang => $namespaces) {
            foreach ($namespaces as $namespace => $translations) {
                foreach ($translations as $key => $string) {
                    if (!is_array($string)) {
                        $result[] = [
                            'lang' => $lang,
                            'namespace' => $namespace,
                            'key' => $key,
                            'string' => $string
                        ];
                    }
                }
            }
        }

        if ($total) {
            $this->error(sprintf('%d unused translation found', $total));
            $this->table($this->headers, $result);
        } else {
            $this->comment('No unused translation');
        }
    }
}
