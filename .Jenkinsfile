pipeline {
    agent {
        label 'builder'
    }
    stages {
        stage('Tests') {
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
                sh(
                    label: 'Install/Update sources from Composer',
                    script: "composer install --no-interaction --no-ansi --no-progress"
                )
                sh(
                    label: 'Run backend tests',
                    script: './bin/phpunit'
                )
            }
        }
    }
}
