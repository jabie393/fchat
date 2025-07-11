$(document).ready(function () {
    $('#receiverSelect').select2({
        width: '100%',
        placeholder: 'Select User',
        allowClear: true
    }).on('select2:open', function () {
        setTimeout(function () {
            document.querySelector('.select2-search__field').focus();
        }, 100);
    });

    // Event change untuk select2
    $('#receiverSelect').on('change', function () {
        receiver_id = Number($(this).val());
        isUserScrolling = false;
        lastMessagesCount = 0;
        loadMessages(true);
        if (receiver_id) {
            $('#messageInput').focus();
        }
    });
});

let receiver_id = document.getElementById('receiverSelect').value;
let isMobile = /Mobi|Android/i.test(navigator.userAgent);
let isUserScrolling = false;
let lastScrollPosition = 0;
let lastMessagesCount = 0;
let refreshInterval;

function getRoomId() {
    if (!receiver_id) return null;
    return user_id < receiver_id
        ? `user_${user_id}_user_${receiver_id}`
        : `user_${receiver_id}_user_${user_id}`;
}

function loadMessages(forceScroll = false) {
    const messagesDiv = document.getElementById('messages');
    const noChatDiv = document.getElementById('noChatSelected');
    const room_id = getRoomId();

    if (!room_id) {
        if (noChatDiv) {
            noChatDiv.style.display = 'flex';
            messagesDiv.innerHTML = '';
            messagesDiv.appendChild(noChatDiv);
        }
        return;
    }

    if (noChatDiv) noChatDiv.style.display = 'none';

    // Simpan posisi scroll sebelum update
    const prevScrollHeight = messagesDiv.scrollHeight;
    const prevScrollTop = messagesDiv.scrollTop;

    fetch('../services/get_messages.php?room_id=' + encodeURIComponent(room_id))
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                messagesDiv.innerHTML = `
                    <div class="no-chat-selected">
                        <i class='bx bx-message-rounded-detail no-chat-icon'></i>
                        <h4>No messages yet</h4>
                        <p>Send your first message to start the conversation</p>
                    </div>
                `;
                return;
            }

            // Cek apakah ada pesan baru
            const hasNewMessages = data.length > lastMessagesCount;
            lastMessagesCount = data.length;

            // Jika user sedang scroll dan tidak ada pesan baru, jangan update DOM
            if (isUserScrolling && !hasNewMessages) {
                return;
            }

            // Jika user tidak scroll atau ada pesan baru, update DOM
            messagesDiv.innerHTML = '';
            data.forEach(msg => {
                const messageDiv = document.createElement('div');
                // Tambahkan class 'no-anim' jika bukan forceScroll (interval)
                messageDiv.className = 'message ' + (msg.sender_id == user_id ? 'me' : 'other') + (forceScroll ? '' : ' no-anim');

                const messageText = document.createElement('div');
                messageText.textContent = msg.message;
                messageDiv.appendChild(messageText);

                // Add timestamp
                const timeDiv = document.createElement('div');
                timeDiv.className = 'message-time';
                const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                timeDiv.textContent = time;
                messageDiv.appendChild(timeDiv);

                // Add read indicator for sent messages
                if (msg.sender_id == user_id && msg.is_read == 1) {
                    const check = document.createElement('i');
                    check.className = 'bx bx-check-double read-indicator';
                    check.style.color = 'white';
                    timeDiv.appendChild(check);
                }

                messagesDiv.appendChild(messageDiv);
            });

            // Atur posisi scroll setelah update
            if (forceScroll || !isUserScrolling || hasNewMessages) {
                setTimeout(() => {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }, 50);
            } else {
                // Pertahankan posisi scroll sebelumnya
                const newScrollHeight = messagesDiv.scrollHeight;
                messagesDiv.scrollTop = prevScrollTop + (newScrollHeight - prevScrollHeight);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const text = input.value.trim();
    if (!text || !receiver_id) return;

    const room_id = getRoomId();
    fetch('../services/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `sender_id=${user_id}&receiver_id=${receiver_id}&room_id=${encodeURIComponent(room_id)}&message=${encodeURIComponent(text)}`
    }).then(() => {
        input.value = '';
        loadMessages(true); // Force scroll to bottom
        // Focus input again after sending
        setTimeout(() => input.focus(), 100);
    }).catch(error => {
        console.error('Error sending message:', error);
    });
}

function setupEventListeners() {
    const messagesDiv = document.getElementById('messages');
    const receiverSelect = document.getElementById('receiverSelect');

    // Deteksi saat user scroll
    messagesDiv.addEventListener('scroll', function () {
        const threshold = 100;
        const position = messagesDiv.scrollTop + messagesDiv.clientHeight;
        const height = messagesDiv.scrollHeight;

        // Jika scroll tidak di paling bawah, anggap user sedang melihat chat lama
        isUserScrolling = position < height - threshold;
        lastScrollPosition = messagesDiv.scrollTop;
    });

    // Saat memilih user baru
    receiverSelect.addEventListener('change', function () {
        receiver_id = this.value;
        isUserScrolling = false; // Reset scroll state
        lastMessagesCount = 0; // Reset message count
        loadMessages(true); // Force scroll to bottom

        if (receiver_id) {
            document.getElementById('messageInput').focus();
        }
    });

    // Auto-resize input based on content
    document.getElementById('messageInput').addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Handle keyboard on mobile devices
    // Tambahkan ini di bagian setupEventListeners()
    if (isMobile) {
        const inputContainer = document.querySelector('.input-container');
        const chatContainer = document.querySelector('.chat-container');
        const header = document.querySelector('.header');
        let originalChatHeight = '';

        // Fungsi untuk menangani perubahan ukuran layar (keyboard muncul/hilang)
        function handleResize() {
            const isKeyboardOpen = (window.innerHeight < window.outerHeight - 100) ||
                (window.visualViewport && (window.visualViewport.height < window.innerHeight));

            if (isKeyboardOpen) {
                // Simpan tinggi asli sebelum diubah
                if (!originalChatHeight) {
                    originalChatHeight = chatContainer.style.height;
                }

                // Hitung tinggi baru untuk chat container
                const headerHeight = header ? header.offsetHeight : 0;
                const inputHeight = inputContainer.offsetHeight;
                const newHeight = window.innerHeight - headerHeight - inputHeight;

                chatContainer.style.height = `${newHeight}px`;
                chatContainer.style.overflow = 'auto';

                // Scroll ke bawah setelah perubahan layout
                setTimeout(() => {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }, 100);
            } else {
                // Kembalikan ke tinggi aslinya saat keyboard hilang
                chatContainer.style.height = originalChatHeight || '';
                chatContainer.style.overflow = '';
            }
        }

        // Gunakan visualViewport API jika tersedia (lebih akurat)
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', handleResize);
        } else {
            window.addEventListener('resize', handleResize);
        }

        // Juga panggil saat input focus/blur sebagai fallback
        const messageInput = document.getElementById('messageInput');
        messageInput.addEventListener('focus', () => {
            setTimeout(handleResize, 300); // Delay untuk memastikan keyboard sudah muncul
        });

        messageInput.addEventListener('blur', handleResize);
    }
}

function startRefreshInterval() {
    // Hentikan interval sebelumnya jika ada
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }

    // Mulai interval baru
    refreshInterval = setInterval(() => {
        loadMessages();
    }, 3000);
}

// Initialize
setupEventListeners();
startRefreshInterval();

// Focus input when receiver is selected
if (receiver_id) {
    document.getElementById('messageInput').focus();
}