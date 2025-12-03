const { default: makeWASocket, DisconnectReason, useMultiFileAuthState, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const express = require('express');
const bodyParser = require('body-parser');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const qrcode = require('qrcode-terminal');

const app = express();
app.use(bodyParser.json());

let sock = null;
let isConnected = false;

const authDir = path.join(__dirname, 'auth_info_baileys');
if (!fs.existsSync(authDir)) {
    fs.mkdirSync(authDir, { recursive: true });
}

const logger = pino({
    level: 'info',
    transport: {
        target: 'pino-pretty',
        options: { colorize: true }
    }
});

async function connectToWhatsApp() {
    try {
        const { state, saveCreds } = await useMultiFileAuthState(authDir);
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            auth: state,
            logger: pino({ level: 'silent' }),
            browser: ['Moroccan Job Scraper', 'Chrome', '10.0']
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            // Display QR code
            if (qr) {
                console.log('\nScan this QR code with WhatsApp:\n');
                qrcode.generate(qr, { small: true });
                console.log('\n');
            }

            if (connection === 'close') {
                const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                isConnected = false;
                logger.warn('Connection closed');
                
                if (shouldReconnect) {
                    logger.info('Reconnecting...');
                    setTimeout(() => connectToWhatsApp(), 3000);
                } else {
                    logger.error('Logged out! Delete auth_info_baileys and restart.');
                }
            } else if (connection === 'open') {
                logger.info('WhatsApp connected successfully!');
                isConnected = true;
            }
        });

    } catch (error) {
        logger.error('Error:', error.message);
        setTimeout(() => connectToWhatsApp(), 5000);
    }
}

function formatPhoneNumber(phone) {
    let cleaned = phone.replace(/[^0-9+]/g, '');
    if (cleaned.startsWith('+212')) cleaned = cleaned.substring(1);
    else if (cleaned.startsWith('0')) cleaned = '212' + cleaned.substring(1);
    else if (!cleaned.startsWith('212')) cleaned = '212' + cleaned;
    return cleaned + '@s.whatsapp.net';
}

app.get('/health', (req, res) => {
    res.json({ success: true, connected: isConnected });
});

app.post('/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({ success: false, error: 'Phone and message required' });
        }

        if (!isConnected || !sock) {
            return res.status(503).json({ success: false, error: 'WhatsApp not connected' });
        }

        const formattedPhone = formatPhoneNumber(phone);
        await sock.sendMessage(formattedPhone, { text: message });

        logger.info('Message sent to:', phone);
        res.json({ success: true, phone });

    } catch (error) {
        logger.error('Error:', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

app.get('/status', (req, res) => {
    res.json({ connected: isConnected, uptime: process.uptime() });
});

const PORT = 3000;
app.listen(PORT, () => {
    logger.info(`WhatsApp Service running on port ${PORT}`);
    logger.info(`Health: http://localhost:${PORT}/health`);
    logger.info(`Status: http://localhost:${PORT}/status`);
    console.log('\n🔄 Connecting to WhatsApp...\n');
    connectToWhatsApp();
});

process.on('SIGINT', async () => {
    logger.info('Shutting down...');
    if (sock) await sock.logout();
    process.exit(0);
});
