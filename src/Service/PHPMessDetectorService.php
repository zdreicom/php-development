<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Service;

use PHPMD\RuleSetFactory;
use PHPMD\TextUI\Command;
use PHPMD\TextUI\CommandLineOptions;

class PHPMessDetectorService
{
    /**
     * @param mixed[] $server
     * @return int
     */
    public function run(array $server): int
    {
        \error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

        $this->initTimeZone();
        $this->initAlias();
        $this->initMemory();

        // Check php setup for cli arguments
        if (array_key_exists('argv', $server) === false) {
            fwrite(STDERR, 'Please enable the "register_argc_argv" directive in your php.ini' . PHP_EOL);
            return 1;
        }

        $argv = (array) $server['argv'];
        // Run command line interface
        return $this->runMain($argv);
    }

    protected function initTimeZone(): void
    {
        if (\ini_get('date.timezone') === '') {
            \date_default_timezone_set('UTC');
        }
    }

    protected function initMemory(): void
    {
        // Allow as much memory as possible by default
        if (extension_loaded('suhosin') && is_numeric(ini_get('suhosin.memory_limit'))) {
            $limit = \ini_get('memory_limit');
            if ($limit === false) {
                $limit = '-1';
            }
            if (preg_match('(^(\d+)([BKMGT]))', $limit, $match)) {
                $shift = ['B' => 0, 'K' => 10, 'M' => 20, 'G' => 30, 'T' => 40];
                $limit = ($match[1] * (1 << $shift[$match[2]]));
            }
            if (ini_get('suhosin.memory_limit') > $limit && $limit > -1) {
                ini_set('memory_limit', ini_get('suhosin.memory_limit'));
            }
            return;
        }
        \ini_set('memory_limit', '-1');
    }

    protected function initAlias(): void
    {
        class_alias('PHPMD\\AbstractNode', 'PHP_PMD_AbstractNode');
        class_alias('PHPMD\\AbstractRenderer', 'PHP_PMD_AbstractRenderer');
        class_alias('PHPMD\\AbstractRule', 'PHP_PMD_AbstractRule');
        class_alias('PHPMD\\Parser', 'PHP_PMD_Parser');
        class_alias('PHPMD\\ParserFactory', 'PHP_PMD_ParserFactory');
        class_alias('PHPMD\\ProcessingError', 'PHP_PMD_ProcessingError');
        class_alias('PHPMD\\Report', 'PHP_PMD_Report');
        class_alias('PHPMD\\Rule', 'PHP_PMD_Rule');
        class_alias('PHPMD\\RuleClassFileNotFoundException', 'PHP_PMD_RuleClassFileNotFoundException');
        class_alias('PHPMD\\RuleClassNotFoundException', 'PHP_PMD_RuleClassNotFoundException');
        class_alias('PHPMD\\RuleSet', 'PHP_PMD_RuleSet');
        class_alias('PHPMD\\RuleSetFactory', 'PHP_PMD_RuleSetFactory');
        class_alias('PHPMD\\RuleSetNotFoundException', 'PHP_PMD_RuleSetNotFoundException');
        class_alias('PHPMD\\RuleViolation', 'PHP_PMD_RuleViolation');
        class_alias('PHPMD\\Node\\AbstractCallableNode', 'PHP_PMD_Node_AbstractCallable');
        class_alias('PHPMD\\Node\\AbstractNode', 'PHP_PMD_Node_AbstractNode');
        class_alias('PHPMD\\Node\\AbstractTypeNode', 'PHP_PMD_Node_AbstractType');
        class_alias('PHPMD\\Node\\ASTNode', 'PHP_PMD_Node_ASTNode');
        class_alias('PHPMD\\Node\\Annotation', 'PHP_PMD_Node_Annotation');
        class_alias('PHPMD\\Node\\Annotations', 'PHP_PMD_Node_Annotations');
        class_alias('PHPMD\\Node\\ClassNode', 'PHP_PMD_Node_Class');
        class_alias('PHPMD\\Node\\FunctionNode', 'PHP_PMD_Node_Function');
        class_alias('PHPMD\\Node\\InterfaceNode', 'PHP_PMD_Node_Interface');
        class_alias('PHPMD\\Node\\MethodNode', 'PHP_PMD_Node_Method');
        class_alias('PHPMD\\Node\\TraitNode', 'PHP_PMD_Node_Trait');
        class_alias('PHPMD\\Renderer\\HTMLRenderer', 'PHP_PMD_Renderer_HTMLRenderer');
        class_alias('PHPMD\\Renderer\\TextRenderer', 'PHP_PMD_Renderer_TextRenderer');
        class_alias('PHPMD\\Renderer\\XMLRenderer', 'PHP_PMD_Renderer_XMLRenderer');
        class_alias('PHPMD\\Renderer\\JSONRenderer', 'PHP_PMD_Renderer_JSONRenderer');
        class_alias('PHPMD\\Rule\\AbstractLocalVariable', 'PHP_PMD_Rule_AbstractLocalVariable');
        class_alias('PHPMD\\Rule\\CyclomaticComplexity', 'PHP_PMD_Rule_CyclomaticComplexity');
        class_alias('PHPMD\\Rule\\ClassAware', 'PHP_PMD_Rule_IClassAware');
        class_alias('PHPMD\\Rule\\ExcessivePublicCount', 'PHP_PMD_Rule_ExcessivePublicCount');
        class_alias('PHPMD\\Rule\\FunctionAware', 'PHP_PMD_Rule_IFunctionAware');
        class_alias('PHPMD\\Rule\\InterfaceAware', 'PHP_PMD_Rule_IInterfaceAware');
        class_alias('PHPMD\\Rule\\MethodAware', 'PHP_PMD_Rule_IMethodAware');
        class_alias('PHPMD\\Rule\\UnusedFormalParameter', 'PHP_PMD_Rule_UnusedFormalParameter');
        class_alias('PHPMD\\Rule\\UnusedLocalVariable', 'PHP_PMD_Rule_UnusedLocalVariable');
        class_alias('PHPMD\\Rule\\UnusedPrivateField', 'PHP_PMD_Rule_UnusedPrivateField');
        class_alias('PHPMD\\Rule\\UnusedPrivateMethod', 'PHP_PMD_Rule_UnusedPrivateMethod');
        class_alias('PHPMD\\Rule\\CleanCode\\BooleanArgumentFlag', 'PHP_PMD_Rule_CleanCode_BooleanArgumentFlag');
        class_alias('PHPMD\\Rule\\CleanCode\\ElseExpression', 'PHP_PMD_Rule_CleanCode_ElseExpression');
        class_alias('PHPMD\\Rule\\CleanCode\\StaticAccess', 'PHP_PMD_Rule_CleanCode_StaticAccess');
        class_alias('PHPMD\\Rule\\Controversial\\CamelCaseClassName', 'PHP_PMD_Rule_Controversial_CamelCaseClassName');
        class_alias('PHPMD\\Rule\\Controversial\\CamelCaseMethodName', 'PHP_PMD_Rule_Controversial_CamelCaseMethodName');
        class_alias('PHPMD\\Rule\\Controversial\\CamelCaseParameterName', 'PHP_PMD_Rule_Controversial_CamelCaseParameterName');
        class_alias('PHPMD\\Rule\\Controversial\\CamelCasePropertyName', 'PHP_PMD_Rule_Controversial_CamelCasePropertyName');
        class_alias('PHPMD\\Rule\\Controversial\\CamelCaseVariableName', 'PHP_PMD_Rule_Controversial_CamelCaseVariableName');
        class_alias('PHPMD\\Rule\\Controversial\\Superglobals', 'PHP_PMD_Rule_Controversial_Superglobals');
        class_alias('PHPMD\\Rule\\Design\\CouplingBetweenObjects', 'PHP_PMD_Rule_Design_CouplingBetweenObjects');
        class_alias('PHPMD\\Rule\\Design\\DepthOfInheritance', 'PHP_PMD_Rule_Design_DepthOfInheritance');
        class_alias('PHPMD\\Rule\\Design\\EvalExpression', 'PHP_PMD_Rule_Design_EvalExpression');
        class_alias('PHPMD\\Rule\\Design\\ExitExpression', 'PHP_PMD_Rule_Design_ExitExpression');
        class_alias('PHPMD\\Rule\\Design\\GotoStatement', 'PHP_PMD_Rule_Design_GotoStatement');
        class_alias('PHPMD\\Rule\\Design\\LongClass', 'PHP_PMD_Rule_Design_LongClass');
        class_alias('PHPMD\\Rule\\Design\\LongMethod', 'PHP_PMD_Rule_Design_LongMethod');
        class_alias('PHPMD\\Rule\\Design\\LongParameterList', 'PHP_PMD_Rule_Design_LongParameterList');
        class_alias('PHPMD\\Rule\\Design\\NpathComplexity', 'PHP_PMD_Rule_Design_NpathComplexity');
        class_alias('PHPMD\\Rule\\Design\\NumberOfChildren', 'PHP_PMD_Rule_Design_NumberOfChildren');
        class_alias('PHPMD\\Rule\\Design\\TooManyFields', 'PHP_PMD_Rule_Design_TooManyFields');
        class_alias('PHPMD\\Rule\\Design\\TooManyMethods', 'PHP_PMD_Rule_Design_TooManyMethods');
        class_alias('PHPMD\\Rule\\Design\\WeightedMethodCount', 'PHP_PMD_Rule_Design_WeightedMethodCount');
        class_alias('PHPMD\\Rule\\Naming\\BooleanGetMethodName', 'PHP_PMD_Rule_Naming_BooleanGetMethodName');
        class_alias('PHPMD\\Rule\\Naming\\ConstantNamingConventions', 'PHP_PMD_Rule_Naming_ConstantNamingConventions');
        class_alias('PHPMD\\Rule\\Naming\\ConstructorWithNameAsEnclosingClass', 'PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass');
        class_alias('PHPMD\\Rule\\Naming\\LongVariable', 'PHP_PMD_Rule_Naming_LongVariable');
        class_alias('PHPMD\\Rule\\Naming\\ShortMethodName', 'PHP_PMD_Rule_Naming_ShortMethodName');
        class_alias('PHPMD\\Rule\\Naming\\ShortVariable', 'PHP_PMD_Rule_Naming_ShortVariable');
        class_alias('PHPMD\\TextUI\\Command', 'PHP_PMD_TextUI_Command');
        class_alias('PHPMD\\TextUI\\CommandLineOptions', 'PHP_PMD_TextUI_CommandLineOptions');
        class_alias('PHPMD\\Writer\\StreamWriter', 'PHP_PMD_Writer_Stream');
    }

    /**
     * @param mixed[] $args
     * @return int
     */
    public function runMain(array $args): int
    {
        try {
            $ruleSetFactory = new RuleSetFactory();
            $options = new CommandLineOptions($args, $ruleSetFactory->listAvailableRuleSets());
            $command = new Command();

            $exitCode = $command->run($options, $ruleSetFactory);
        } catch (\Exception $e) {
            \fwrite(STDERR, $e->getMessage() . PHP_EOL);
            $exitCode = Command::EXIT_EXCEPTION;
        }
        return $exitCode;
    }
}
