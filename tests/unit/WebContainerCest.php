<?php


class WebContainerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function checkContainerIsRunning(UnitTester $I){
        $I->wantTo("verify centos container up and running");
        $I->runShellCommand("docker inspect -f {{.State.Running}} test_web");
        $I->seeInShellOutput("true");
    }


    public function checkPHPVersion(UnitTester $I){
        $I->wantTo("verify php 7.0 is installed in the container");
        $I->runShellCommand("docker exec test_web php --version");
        $I->seeInShellOutput('PHP 7.0');
    }

    public function checkPHPUnitVersion(UnitTester $I){
        $I->wantTo("verify phpunit library is installed in the container");
        $I->runShellCommand("docker exec test_web phpunit --version");
        $I->seeInShellOutput('PHPUnit 5.7.21');
    }

    public function checkNcCommand(UnitTester $I){
        $I->wantTo("verify nc command is installed in the image");
        $I->runShellCommand("docker exec test_web nc -h");
        $I->seeInShellOutput('Ncat');
    }

    public function checkXdebugVersion(AcceptanceTester $I){
        $I->wantTo("verify xdebug is installed in the image");
        $I->runShellCommand("docker exec test_web bash -c 'yum info php-pecl-xdebug | grep Version'");
        $I->seeInShellOutput('Version');
        $I->seeInShellOutput('2.');
    }

    public function checkAstIsInstalled(UnitTester $I){
        $I->wantTo("verify ast module is installed in the image");
        $I->runShellCommand("docker exec test_web php -m");
        $I->seeInShellOutput('ast');
    }

    public function checkStatsIsInstalled(UnitTester $I){
        $I->wantTo("verify stats module is installed in the image");
        $I->runShellCommand("docker exec test_web php -m");
        $I->seeInShellOutput('stats');
    }
}
