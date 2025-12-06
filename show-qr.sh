#!/bin/bash

echo "Checking WhatsApp connection..."

# Check current status
STATUS=$(curl -s http://localhost:3000/health | grep -o '"connected":[^,]*')

if [[ $STATUS == *"true"* ]]; then
    echo "✅ WhatsApp is already connected!"
    echo "No need to scan QR code."
else
    echo "❌ WhatsApp not connected. Generating QR code..."
    echo ""
    
    # Restart to generate QR
    pm2 restart whatsapp-service
    
    echo "⏳ Waiting for QR code..."
    sleep 3
    
    echo ""
    echo "📱 SCAN THIS QR CODE:"
    echo "===================="
    pm2 logs whatsapp-service --lines 50
fi
