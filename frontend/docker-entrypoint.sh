#!/bin/sh
set -e

# Create .env file if it doesn't exist (for Vite environment variables)
if [ ! -f .env ]; then
    echo "Creating .env file for Vite..."
    if [ -n "$VITE_API_URL" ]; then
        echo "VITE_API_URL=${VITE_API_URL}" > .env
    else
        echo "VITE_API_URL=http://localhost" > .env
    fi
fi

# Install npm dependencies if node_modules doesn't exist or vite is missing
if [ ! -d "node_modules" ] || [ ! -f "node_modules/.bin/vite" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

echo "Starting Vite development server..."

exec npm run dev -- --host

