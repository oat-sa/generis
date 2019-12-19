pipeline {
    agent {
        label 'master'
    }
    stages {
        stage('Resolve TAO dependencies') {
            environment {
                GITHUB_ORGANIZATION='oat-sa'
                REPO_NAME='oat-sa/generis'
            }
            steps {
                sh(
                    label : 'Create build directory',
                    script: 'mkdir -p build'
                )

                withCredentials([string(credentialsId: 'jenkins_github_token', variable: 'GIT_TOKEN')]) {
                    sh(
                        label : 'Run the Dependency Resolver',
                        script: '''
changeBranch=$CHANGE_BRANCH
TEST_BRANCH="${changeBranch:-$BRANCH_NAME}"
echo "select branch : ${TEST_BRANCH}"
docker run --rm  \\
-e "GITHUB_ORGANIZATION=${GITHUB_ORGANIZATION}" \\
-e "GITHUB_SECRET=${GIT_TOKEN}"  \\
registry.service.consul:4444/tao/dependency-resolver oat:dependencies:resolve --main-branch ${TEST_BRANCH} --repository-name ${REPO_NAME} > build/composer.json
                        '''
                    )
                }
            }
        }
        stage('Install') {
            agent {
                docker {
                    image 'alexwijn/docker-git-php-composer'
                    reuseNode true
                }
            }
            environment {
                HOME = '.'
            }
            options {
                skipDefaultCheckout()
            }
            steps {
                dir('build') {
                    sh(
                        label: 'Install/Update sources from Composer',
                        script: 'COMPOSER_DISCARD_CHANGES=true composer update --no-interaction --no-ansi --no-progress --no-scripts'
                    )
                    sh(
                        label: 'Add phpunit',
                        script: 'composer require phpunit/phpunit:^4.8'
                    )
                }
            }
        }
        stage('Tests') {
            parallel {
                stage('Backend Tests') {
                    agent {
                        docker {
                            image 'alexwijn/docker-git-php-composer'
                            reuseNode true
                        }
                    }
                    options {
                        skipDefaultCheckout()
                    }
                    steps {
                        dir('build'){
                            sh(
                                label: 'Run backend tests',
                                script: './vendor/bin/phpunit generis/test/unit'
                            )
                        }
                    }
                }
            }
        }
    }
}
