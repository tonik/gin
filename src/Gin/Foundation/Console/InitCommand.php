<?php

namespace Tonik\Gin\Foundation\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class InitCommand extends Command
{
    /**
     * List of theme details entries.
     *
     * @var array
     */
    protected $questions = [
        'theme.name' => 'Enter theme name: ',
        'theme.url' => 'Enter theme url: ',
        'theme.description' => 'Enter theme description: ',
        'theme.version' => 'Enter theme version: ',
        'theme.author' => 'Enter theme author: ',
        'theme.uri' => 'Enter theme uri: ',
        'theme.textdomain' => 'Enter theme textdomain: ',
        'theme.namespace' => 'Enter theme namespace: ',
    ];

    /**
     * Details of the theme.
     *
     * @var array
     */
    protected $answers = [];

    /**
     * Console input.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * Console output.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('theme:init')
            ->setDescription('Initializes starter theme.');
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);

        $this->askQuestions();

        if ($this->askForConfirmation()) {
            $this->rename();

            return;
        }

        $output->writeln('<error>Theme initiation abored.</error>');
    }

    /**
     * Ask for theme details.
     *
     * @return void
     */
    public function askQuestions()
    {
        $entries = $this->getQuestions();

        if ($this->hasQuestions()) {
            list($key, $value) = [key($entries), reset($entries)];

            $this->unsetQuestion($key);

            $this->askForDetail($key, $value);
        }
    }

    /**
     * Ask for specific theme detail.
     *
     * @param  array $detail
     *
     * @return void
     */
    public function askForDetail($key, $value)
    {
        $question = new Question("<fg=yellow>{$value}</>");

        $question->setValidator(function ($answer) {
            $value = trim($answer);

            if ( ! empty($value)) {
                return $value;
            }

            throw new RuntimeException('You have to answer to this question.');
        });

        $answer = $this->getHelper('question')
            ->ask($this->getInput(), $this->getOutput(), $question);

        $this->setAnswer($key, $answer);

        $this->askQuestions();
    }

    /**
     * Asks user for confiramtion before renaming.
     *
     * @return boolean
     */
    public function askForConfirmation()
    {
        $question = new ChoiceQuestion(
            'Ready to start initialization procedure. Want to continue?',
            ['yes', 'no'],
            1
        );

        $question->setErrorMessage('Answer with `yes` or `no`.');

        $answer = $this->getHelper('question')
            ->ask($this->getInput(), $this->getOutput(), $question);

        if ('no' === $answer) {
            return false;
        }

        return true;
    }

    /**
     * Renames theme deatils.
     *
     * @return void
     */
    protected function rename()
    {
        foreach ($this->answers as $key => $answer) {
            $this->findAndReplaceInDir('./', "{{ {$key} }}", $answer);
        }

        $this->getOutput()->writeln('<fg=green>Theme successufully initialized. Cheers!</>');
    }

    /**
     * Finds and replaces string for all files in directory.
     *
     * @param  string $where
     * @param  string $search
     * @param  string $replace
     *
     * @return void
     */
    private function findAndReplaceInDir($where, $search, $replace)
    {
        exec("find {$where} -type f \( -name  \*.php -o -name \*.css \) -exec \
            sed -i '' 's/{$search}/{$replace}/g' {} +");
    }

    /**
     * Finds and replaces string in file.
     *
     * @param  string $where
     * @param  string $search
     * @param  string $replace
     *
     * @return void
     */
    private function findAndReplaceInFile($where, $search, $replace)
    {
        exec("sed -i '' 's/{$search}/{$replace}/g' {$where}");
    }

    /**
     * Gets the List of theme details entries.
     *
     * @return array
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Checks if details has entries.
     *
     * @return boolean
     */
    public function hasQuestions()
    {
        return  ! empty($this->questions);
    }

    /**
     * Unsets entry in details.
     *
     * @param  string $key
     *
     * @return void
     */
    public function unsetQuestion($key)
    {
        unset($this->questions[$key]);
    }

    /**
     * Sets entry in details answers.
     *
     * @param  string $key
     * @param  string $value
     *
     * @return void
     */
    public function setAnswer($key, $value)
    {
        $this->answers[$key] = addslashes($value);

        return $this;
    }

    /**
     * Gets entry in details answers.
     *
     * @param  string $key
     *
     * @return void
     */
    public function getAnswer($key)
    {
        return $this->answers[$key];
    }

    /**
     * Gets the Console input.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Sets the Console input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input the input
     *
     * @return self
     */
    protected function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Gets the Console output.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets the Console output.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output the output
     *
     * @return self
     */
    protected function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }
}
