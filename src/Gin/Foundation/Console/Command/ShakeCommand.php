<?php

namespace Tonik\Gin\Foundation\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class ShakeCommand extends Command
{
    /**
     * List of theme details entries.
     *
     * @var array
     */
    protected $questions = [
        'theme.name' => 'Theme Name [theme.name]',
        'theme.uri' => 'Theme URI [theme.uri]',
        'theme.description' => 'Theme Description [theme.description]',
        'theme.version' => 'Theme Version [theme.version]',
        'theme.author' => 'Author [theme.author]',
        'theme.author.uri' => 'Author URI [theme.uri]',
        'theme.textdomain' => 'Theme Textdomain [theme.textdomain]',
        'theme.namespace' => 'Theme Namespace [theme.namespace]',
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
        $this->setName('tonik:shake')
            ->setDescription('Starts setup wizard guide, which initializes starter theme.');
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
            $output->writeln('<info>Initializing theme ...</info>');

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
        $question = new Question("<question>{$value}:</question> ");

        $question->setNormalizer(function ($value) {
            return trim($value);
        });

        $question->setValidator(function ($answer) {
            if (! empty($answer)) {
                return $answer;
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
        setlocale(LC_CTYPE, "en_US.UTF-8");

        foreach ($this->answers as $key => $answer) {
            $this->findAndReplaceInDir(
                './',
                "{{ {$key} }}",
                escapeshellcmd($answer)
            );
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
        exec("find {$where} -type f \( -name \*.php -o -name \*.css -o -name \*.json \) -exec \
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
