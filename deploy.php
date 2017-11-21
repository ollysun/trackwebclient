<?php
/**
 * Author: Adeyemi Olaoye <yemi@cottacush.com>
 * Date: 08/03/2016
 * Time: 12:50 PM
 */

require 'recipe/yii.php';

serverList('deploy/servers.yml');

set('writable_dirs', ['runtime', 'web/assets']);

env('composer_options', 'install --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction');

env('local_path', dirname(__FILE__));


/**
 * Cleanup old releases.
 */
task('cleanup', function () {
    $releases = env('releases_list');

    $keep = get('keep_releases');

    while ($keep > 0) {
        array_shift($releases);
        --$keep;
    }

    foreach ($releases as $release) {
        run("sudo rm -rf {{deploy_path}}/releases/$release");
    }

    run("cd {{deploy_path}} && if [ -e release ]; then rm release; fi");
    run("cd {{deploy_path}} && if [ -h release ]; then rm release; fi");

})->desc('Cleaning up old releases');

task('tag_release', function () {

    $promptAnswer = null;

    while (!in_array(strtolower(trim($promptAnswer)), ['y', 'n', 'yes', 'no'])) {
        $promptAnswer = ask('Tag Release? (y/n)');
    }

    if (!in_array($promptAnswer, ['y', 'yes'])) {
        return;
    }

    writeln('Tagging Release... ');
    runLocally('cd ' . env('local_path'));
    runLocally('git stash');
    runLocally('git checkout master');
    runLocally('git pull origin master');
    $result = runLocally('git for-each-ref --format="%(tag)" --sort=-taggerdate  refs/tags');
    $currentTag = current($result->toArray());
    $releaseVersion = '';

    while (strlen(trim($releaseVersion)) == 0) {
        $releaseVersion = ask('Enter Release Version (Current: ' . $currentTag . '): ');
    }

    $releaseMessage = ask('Enter Release Message: ');

    runLocally('git tag -a ' . $releaseVersion . ' -m "' . $releaseMessage . '"');
    runLocally('git push --tags');
    runLocally('git checkout develop');
    writeln('Release Tagged Successfully');

})->onlyForStage('production');

task('update_production', function () {
    writeln('Updating Production Branch... ');
    runLocally('cd ' . env('local_path'));
    runLocally('git stash');
    runLocally('git checkout master');
    runLocally('git pull origin master');
    runLocally('git checkout production');
    runLocally('git pull origin production');
    runLocally('git merge master');
    runLocally('git push origin production');
    runLocally('git checkout develop');
    writeln('Production Branch Updated Successfully');
})->onlyForStage('production');

/**
 * Main task
 */
task('deploy', [
    'update_production',
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:symlink',
    'deploy:writable',
    'cleanup',
    'tag_release'
])->desc('Deploy Project');

set('repository', 'git@bitbucket.org:cottacush/courierplusng.git');


//slack tasks
task('slack:before_deploy', function () {
    postToSlack('Starting deploy on ' . env('server.name') . '...');
});

task('slack:after_deploy', function () {
    postToSlack('Deploy to ' . env('server.name') . ' done');
});

function postToSlack($message)
{
    runLocally('curl -s -S -X POST --data-urlencode payload="{\"channel\": \"#courier_plus\", \"username\": \"courierplus-FE Release Bot\", \"text\": \"' . $message . '\"}" https://hooks.slack.com/services/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');
}


before('deploy', 'slack:before_deploy');
after('deploy', 'slack:after_deploy');

