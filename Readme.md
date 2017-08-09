# Securing appliciations on Google Container Engine


Examples for creating GKE clusers and patterns for secure applications running in GKE. 


## Notes on using the examples

```
kubectl run -i --tty sdk --image=google/cloud-sdk --restart=Never -- bash
gcloud compute instances create "instance-1"
```

[Using Persistent Disks with WordPress and MySQL](https://cloud.google.com/container-engine/docs/tutorials/persistent-disk/)

```
kubectl create secret generic mysql --from-literal=password=NOTAGREATPASSWORD
gcloud compute disks create --size 200GB mysql-disk --zone="us-central1-a"
gcloud compute disks create --size 200GB wordpress-disk --zone="us-central1-a"
kubectl apply -f example-1-gcp/
```

```
kubectl apply -f example-2-iptables/
```

```
kubectl apply -f example-3-sidecar/
```

```
kubectl exec -it $(kubectl get pods | awk '/wordpress/{print $1}'| head -n 1) -- capsh --print
kubectl apply -f example-4-sec-context/
```

[AppArmor Profile Loader](https://github.com/kubernetes/contrib/tree/master/apparmor/loader)








