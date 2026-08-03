require('dotenv').config();
const axios = require('axios');

const APP_URL      = (process.env.APP_URL || 'http://localhost').replace(/\/$/, '');
const PRINT_TOKEN  = process.env.PRINT_TOKEN || '';

console.log('🔧 Testing EUT Kitchen Print Server Connection');
console.log('─'.repeat(50));
console.log(`Server URL: ${APP_URL}`);
console.log(`Print Token: ${PRINT_TOKEN ? '✓ Set' : '✗ Not set'}`);
console.log('');

async function testConnection() {
    try {
        console.log('Testing server connection...');
        const url = `${APP_URL}/api/print-server/pending-prints`;
        const res = await axios.get(url, {
            headers: { 'X-Print-Token': PRINT_TOKEN },
            timeout: 10000,
        });

        console.log('✅ SUCCESS: Connection established!');
        console.log(`Status: ${res.status}`);
        console.log(`Jobs found: ${res.data.jobs?.length || 0}`);
        
        if (res.data.jobs?.length) {
            console.log('\n📋 Pending print jobs:');
            res.data.jobs.forEach((job, i) => {
                console.log(`  ${i + 1}. Order ${job.order_number} (${job.type})`);
            });
        } else {
            console.log('\n📝 No pending print jobs (this is normal)');
        }
        
        console.log('\n🎉 Print server setup is working correctly!');
        console.log('You can now run "npm start" to begin auto-printing.');
        
    } catch (err) {
        console.log('❌ CONNECTION FAILED');
        
        if (err.code === 'ECONNREFUSED') {
            console.log('Error: Cannot reach the server');
            console.log('Solutions:');
            console.log('  • Check if the server is running');
            console.log('  • Verify APP_URL in .env file');
            console.log('  • Check internet connection');
        } else if (err.response?.status === 401) {
            console.log('Error: Authentication failed');
            console.log('Solutions:');
            console.log('  • Check PRINT_TOKEN in .env file');
            console.log('  • Ensure token matches Laravel PRINT_SERVER_TOKEN');
        } else if (err.code === 'ENOTFOUND') {
            console.log('Error: Server domain not found');
            console.log('Solutions:');
            console.log('  • Check APP_URL spelling in .env');
            console.log('  • Verify domain is accessible');
        } else {
            console.log(`Error: ${err.message}`);
            if (err.response) {
                console.log(`HTTP Status: ${err.response.status}`);
                console.log(`Response: ${err.response.data}`);
            }
        }
    }
}

testConnection();