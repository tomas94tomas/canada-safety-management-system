job "jobName" {
  region = "global"
  namespace = "namespace"
  datacenters = ["datacenter-env"]
  type = "service"

  update {
    max_parallel      = 1
    health_check      = "checks"
    min_healthy_time  = "20s"
    healthy_deadline  = "5m"
    progress_deadline = "10m"
    auto_revert       = true
    auto_promote      = true
    canary            = 1
    stagger           = "30s"
  }

  group "Webapp" {

    count = 1

    volume "localtime_volume" {
      type = "host"
      read_only = true
      source = "localtime_volume"
        }

     volume "jobName_volume_environmentCode" {
      type = "host"
      read_only = false
      source = "jobName_volume_environmentCode"
    }

    network {
      port "https" {
        to = 443
      }
    }

    #
    service {
      port = "https"
      check {
        type     = "tcp"
        path     = "/"
        interval = "10s"
        timeout  = "2s"
      }
    }


    task "frontend" {
        driver = "docker"
        volume_mount {
        volume = "localtime_volume"
        destination = "/etc/localtime"
        read_only = true
            }
        volume_mount {
        volume = "jobName_volume_environmentCode"
        destination = "/var/www/html/storage/app/report_attachments"
        read_only = false
            }
        env {
        imageTag = "latest"
        webConsole = "webAdress"
        dockerImage = "dockerImage"
        jobName = "jobName"
        }
        config {
            image = "rnd-repository.fltechnics.com/${dockerImage}:${imageTag}"
            image_pull_timeout = "3m"
            ports = ["https"]
            volumes = [
                "secrets/certs/fltechnics.com.crt:/etc/ssl/certs/fltechnics.com.crt",
                "secrets/certs//fltechnics.com.key:/etc/ssl/private/fltechnics.com.key",
                "secrets/conf/.env:/var/www/html/.env"
            ]
        }


        resources {
            cpu    = 1000 # MHz
            memory = 1500 # MB
        }
        service {
        name = "serviceName-https"
        tags = [
          "traefik.tags=trk-ext",
          "traefik.http.routers.serviceName.entrypoints=https",
          #"traefik.http.routers.serviceName.rule=Host(`${webConsole}`) && (PathPrefix(`/canada/`))",
          #"traefik.http.routers.serviceName.rule=Host(`${webConsole}`)",
          "traefik.http.routers.serviceName.rule=Host(`safety-canada.fltechnics.com`)",
          "traefik.http.routers.serviceName.tls=true",
          "traefik.http.services.serviceName.loadbalancer.server.scheme=https"

        ]
        port = "https"
        check {
          type = "tcp"
          interval = "10s"
          timeout = "4s"
        }
      }


        template {
            data = <<EOF
{{- with secret "security/data/certificates/fltechnics.com" -}}
{{- .Data.data.certificate -}}
{{- end -}}
EOF
            destination = "secrets/certs/fltechnics.com.crt"
            change_mode = "signal"
            change_signal = "SIGHUP"
        }

        template {
            data = <<EOF
{{- with secret "security/data/certificates/fltechnics.com" -}}
{{- .Data.data.key -}}
{{- end -}}
EOF
            destination = "secrets/certs/fltechnics.com.key"
            change_mode = "signal"
            change_signal = "SIGHUP"
        }

        template {
            data = <<EOF
{{- with secret "security/"config_dir"/config" -}}
{{- .Data.data.env -}}
{{- end -}}
EOF
            destination = "secrets/conf/.env"
            change_mode = "signal"
            change_signal = "SIGHUP"
        }

        vault {
        policies = ["nomad-server", "deployment"]
        }
    }
  }
}
