<?php

namespace Hexadog\TranslationManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
        $total = 0;

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
                    } else {
                        foreach (Arr::dot($string) as $k => $string) {
                            $result[] = [
                                'lang' => $lang,
                                'namespace' => $namespace,
                                'key' => sprintf('%s.%s', $key, $k),
                                'string' => $string
                            ];
                        }
                    }

                    $total++;
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
