<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-30 15:18
 */
namespace Notadd\Foundation\Console;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\Parser;
use Illuminate\Contracts\Foundation\Application as NotaddApplication;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
class Command extends SymfonyCommand {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $notadd;
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;
    /**
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;
    /**
     * @var string
     */
    protected $signature;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;
    public function __construct() {
        if(isset($this->signature)) {
            $this->configureUsingFluentDefinition();
        } else {
            parent::__construct($this->name);
        }
        $this->setDescription($this->description);
        if(!isset($this->signature)) {
            $this->specifyParameters();
        }
    }
    protected function configureUsingFluentDefinition() {
        list($name, $arguments, $options) = Parser::parse($this->signature);
        parent::__construct($name);
        foreach($arguments as $argument) {
            $this->getDefinition()->addArgument($argument);
        }
        foreach($options as $option) {
            $this->getDefinition()->addOption($option);
        }
    }
    protected function specifyParameters() {
        foreach($this->getArguments() as $arguments) {
            call_user_func_array([
                $this,
                'addArgument'
            ], $arguments);
        }
        foreach($this->getOptions() as $options) {
            call_user_func_array([
                $this,
                'addOption'
            ], $options);
        }
    }
    public function run(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = new OutputStyle($input, $output);
        return parent::run($input, $output);
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $method = method_exists($this, 'handle') ? 'handle' : 'fire';
        return $this->notadd->call([
            $this,
            $method
        ]);
    }
    public function call($command, array $arguments = []) {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;
        return $instance->run(new ArrayInput($arguments), $this->output);
    }
    public function callSilent($command, array $arguments = []) {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;
        return $instance->run(new ArrayInput($arguments), new NullOutput);
    }
    public function argument($key = null) {
        if(is_null($key)) {
            return $this->input->getArguments();
        }
        return $this->input->getArgument($key);
    }
    public function option($key = null) {
        if(is_null($key)) {
            return $this->input->getOptions();
        }
        return $this->input->getOption($key);
    }
    public function confirm($question, $default = false) {
        return $this->output->confirm($question, $default);
    }
    public function ask($question, $default = null) {
        return $this->output->ask($question, $default);
    }
    public function anticipate($question, array $choices, $default = null) {
        return $this->askWithCompletion($question, $choices, $default);
    }
    public function askWithCompletion($question, array $choices, $default = null) {
        $question = new Question($question, $default);
        $question->setAutocompleterValues($choices);
        return $this->output->askQuestion($question);
    }
    public function secret($question, $fallback = true) {
        $question = new Question($question);
        $question->setHidden(true)->setHiddenFallback($fallback);
        return $this->output->askQuestion($question);
    }
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null) {
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMaxAttempts($attempts)->setMultiselect($multiple);
        return $this->output->askQuestion($question);
    }
    public function table(array $headers, $rows, $style = 'default') {
        $table = new Table($this->output);
        if($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }
        $table->setHeaders($headers)->setRows($rows)->setStyle($style)->render();
    }
    public function info($string) {
        $this->output->writeln("<info>$string</info>");
    }
    public function line($string) {
        $this->output->writeln($string);
    }
    public function comment($string) {
        $this->output->writeln("<comment>$string</comment>");
    }
    public function question($string) {
        $this->output->writeln("<question>$string</question>");
    }
    public function error($string) {
        $this->output->writeln("<error>$string</error>");
    }
    public function warn($string) {
        $style = new OutputFormatterStyle('yellow');
        $this->output->getFormatter()->setStyle('warning', $style);
        $this->output->writeln("<warning>$string</warning>");
    }
    protected function getArguments() {
        return [];
    }
    protected function getOptions() {
        return [];
    }
    public function getOutput() {
        return $this->output;
    }
    public function getNotadd() {
        return $this->notadd;
    }
    public function setNotadd(NotaddApplication $notadd) {
        $this->notadd = $notadd;
    }
}