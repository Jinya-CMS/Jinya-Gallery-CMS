// Uses Declarative syntax to run commands inside a container.
pipeline {
    triggers {
        pollSCM("*/5 * * * *")
    }
    agent {
        kubernetes {
            yaml '''
apiVersion: v1
kind: Pod
spec:
  imagePullSecrets:
    - name: dev-imanuel-jenkins-regcred
  containers:
  - name: php
    image: registry.imanuel.dev/library/php:7.4-apache
    command:
    - sleep
    args:
    - infinity
  - name: rust
    image: registry.imanuel.dev/library/rust:1.48
    command:
    - sleep
    args:
    - infinity
  - name: package
    image: registry.imanuel.dev/library/alpine:3
    command:
    - sleep
    args:
    - infinity
  - name: upload
    image: registry.imanuel.dev/library/alpine:3
    command:
    - sleep
    args:
    - infinity
  - name: docker
    image: registry.imanuel.dev/library/docker:stable
    command:
    - cat
    tty: true
    volumeMounts:
    - mountPath: /var/run/docker.sock
      name: docker-sock
'''
        }
    }
    stages {
        stage('Build Jinya') {
            parallel {
                stage('Build designer') {
                    stages {
                        stage('Clone tag') {
                            when {
                                buildingTag()
                            }
                            steps {
                                git branch: "refs/tags/$TAG_NAME", url: 'https://github.com/Jinya-CMS/jinya-designer.git'
                            }
                        }
                        stage('Clone main') {
                            when {
                                branch: 'main'
                            }
                            steps {
                                git branch: "main", url: 'https://github.com/Jinya-CMS/jinya-designer.git'
                            }
                        }
                        stage('Build rust') {
                            steps {
                                container('rust') {
                                    sh 'apt-get install nodejs'
                                    sh 'yarn'
                                    sh 'yarn build:prod'
                                    sh "zip -r ./jinya-designer.zip ./* --exclude .git/ --exclude src/ --exclude node_modules/ --exclude target/"
                                    stash includes: 'jinya-designer.zip', name: 'jinya-designer'
                                }
                            }
                        }
                    }
                }
                stage('Build backend') {
                    stages {
                        stage('Clone tag') {
                            when {
                                buildingTag()
                            }
                            steps {
                                git branch: "refs/tags/$TAG_NAME", url: 'https://github.com/Jinya-CMS/jinya-backend.git'
                            }
                        }
                        stage('Clone main') {
                            when {
                                branch: 'main'
                            }
                            steps {
                                git branch: "main", url: 'https://github.com/Jinya-CMS/jinya-backend.git'
                            }
                        }
                        stage('Build php') {
                            steps {
                                container('php') {
                                    git branch: "refs/tags/$TAG_NAME", url: 'https://github.com/Jinya-CMS/jinya-backend.git'
                                    sh "mkdir -p /usr/share/man/man1"
                                    sh "apt-get update"
                                    sh "apt-get install -y apt-utils"
                                    sh "apt-get install -y libzip-dev git wget unzip zip"
                                    sh "docker-php-ext-install pdo pdo_mysql zip"
                                    sh "php --version"
                                    sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                                    sh "php composer-setup.php"
                                    sh '''php -r "unlink(\'composer-setup.php\');"'''
                                    sh 'php composer.phar install --no-dev'
                                    sh "zip -r ./jinya-backend.zip ./* --exclude .git/ --exclude .sonarwork/ --exclude sonar-project.properties"
                                    stash includes: 'jinya-backend.zip', name: 'jinya-backend'
                                }
                            }
                        }
                    }
                }
            }
        }
        stage('Create package') {
            steps {
                container('package') {
                    unstash 'jinya-designer'
                    unstash 'jinya-backend'
                    sh 'apk add zip curl'
                    sh 'unzip jinya-backend.zip -d ./jinya-cms'
                    sh 'unzip jinya-designer.zip -d ./jinya-cms/public'
                    sh 'mv ./jinya-cms/public/index.html /jinya-cms/public/designer/index.html'
                    sh 'cd ./jinya-cms && zip -r ../jinya-cms.zip ./*'
                    stash includes: 'jinya-cms.zip', name: 'jinya-cms'
                }
            }
        }
        stage('Upload new Jinya version') {
            parallel {
                stage('Upload artifact') {
                    when {
                        buildingTag()
                    }
                    environment {
                        JINYA_RELEASES_AUTH = credentials('releases.jinya.de')
                    }
                    steps {
                        container('upload') {
                            unstash 'jinya-cms'
                            sh "curl -X POST -H \"Content-Type: application/octet-stream\" -H \"JinyaAuthKey: ${env.JINYA_RELEASES_AUTH}\" -d @jinya-cms.zip https://releases.jinya.de/cms/push/${env.TAG_NAME}"
                        }
                    }
                }
                stage('Archive artifact') {
                    steps {
                        container('upload') {
                            unstash 'jinya-cms'
                            archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false
                        }
                    }
                }
                stage('Create docker image') {
                    when {
                        buildingTag()
                    }
                    steps {
                        container('docker') {
                            unstash 'jinya-cms'
                            sh 'unzip jinya-cms.zip -d ./jinya-cms'
                            script {
                                def image = docker.build "registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME"
                                docker.withRegistry('https://registry-hosted.imanuel.dev', 'nexus.imanuel.dev') {
                                    image.push()
                                }

                                image.tag('jinyacms/jinya-cms:$TAG_NAME')
                                docker.withRegistry('', 'hub.docker.com') {
                                    image.push()
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
