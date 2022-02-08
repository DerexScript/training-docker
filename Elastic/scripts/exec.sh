sleep 2m
echo "Init" >> /tmp/logExecELasticI.txt
python /scripts/elasticPopulate.py
echo "Terminated" >> /tmp/logExecELastic.txt