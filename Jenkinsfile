pipeline {
    agent any
    stages {
        stage('Preparation') {
            steps {

                sh 'sudo rm -rf ${WORKSPACE}/data'

                checkout([
                    $class: 'GitSCM',
                    branches: [[name: '*/master']],
                    doGenerateSubmoduleConfigurations: false,
                    extensions: [],
                    submoduleCfg: [],
                    userRemoteConfigs: [[credentialsId: 'ssh-jenkins-master', url: 'ssh://git@stash.deubert.it:7999/doc/db-databuilder.git']]
                ])

                sh 'mkdir ${WORKSPACE}/data && chmod 0777 ${WORKSPACE}/data'
            }
        }

        stage('Set Dotenv config') {
            steps {
                sh 'cp .env.dist .env'
            }
        }

        stage('Build docker env') {
            steps {
                sh 'docker-compose -f docker-compose.01-import.yml build'
            }
        }

        stage('Start docker env') {
            steps {
                sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} up -d'
            }
        }

        stage('Validate docker env') {
            steps {
                sh 'docker ps | grep "${JOB_NAME}_db"'
            }
        }

        stage('Wait for import completion') {
            steps {
                sh 'chmod +x ${WORKSPACE}/wait-for-it.sh'
                sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} exec -T db bash -c "chmod +x /tmp/wait-for-it.sh && /tmp/wait-for-it.sh --timeout=30 --host=localhost --port=3306"'
                sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} exec -T db chmod -R 0777 /var/lib/mysql'
            }
        }

        stage('Validate output') {
            steps {
                sh 'rm -f ${WORKSPACE}/examples/01-simple/export/*.sql'
                sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} exec -T db sh -c "/usr/bin/mysqldump -uroot -proot --skip-dump-date mysql user" > ${WORKSPACE}/examples/01-simple/export/mysql-users.sql'
                sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} exec -T db sh -c "/usr/bin/mysqldump -uroot -proot --skip-dump-date test01" > ${WORKSPACE}/examples/01-simple/export/test01.sql'
            }
        }

        stage('Stop docker env') {
            steps {
                sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} stop'
            }
        }

        stage('Push data container to registry') {
            steps {
                echo "not implemented yet"
            }
        }
    }

    post {
        failure {
            sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} logs db'
            sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} exec -T db chmod -R 0777 /var/lib/mysql'
            sh 'docker-compose -f docker-compose.01-import.yml -p ${JOB_NAME} stop'
            //mail to: support@deubert.it, subject: 'The Pipeline failed :('
        }
        always {
            sh 'rm -rf data'
        }
    }
}