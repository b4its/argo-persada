#!/bin/bash
set -e

echo "🌐 Starting Ngrok tunnel..."

# Wait for containers not in restarting status
for i in $(seq 1 10); do
    RESTARTING=$(docker ps -q -f status=restarting | wc -l)
    if [ "$RESTARTING" -eq 0 ]; then
        break
    fi
    echo "⏳ Waiting for containers to stop restarting... ($i/10)"
    sleep 2
done

if [ "$RESTARTING" -ne 0 ]; then
    echo "❌ Container masih dalam status restarting setelah $i tries"
    docker ps -a | grep -E "(restarting|exited)"
    exit 1
fi

# Stop all containers first for fresh start
echo "Stopping all containers for fresh start..."
docker compose -f docker-compose.ngrok.yml down 2>/dev/null || true
sleep 1

# Check and kill processes on ports
echo "Checking and killing processes on ports 9000 and 4040..."
lsof -t -i:9000 2>/dev/null | xargs -r kill -9 || true
lsof -t -i:4040 2>/dev/null | xargs -r kill -9 || true
fuser -k 9000/tcp 2>/dev/null || true
fuser -k 4040/tcp 2>/dev/null || true
sleep 1

# Start containers
echo "Starting Ngrok containers..."
docker compose -f docker-compose.ngrok.yml up -d --force-recreate
sleep 3

echo "✅ Ngrok started successfully!"
