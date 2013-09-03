<?php
// Copyright (C) 2011 BauerUK
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

namespace Wrapper\Curl;

/**
 * Class Curl
 *
 * @package Wrapper\Curl
 * @author BauerUK <https://github.com/BauerUK>
 * @author Andrey Kolchenko <komexx@gmail.com>
 * @property string $version
 * @property int $err_no
 * @property string $error
 */
class Curl
{
    /**
     * @var resource
     */
    protected $curl_handler;
    /**
     * @var array
     */
    private $http200Aliases = array();
    /**
     * @var array
     */
    private $httpHeaders = array();
    /**
     * @var array
     */
    private $postQuotes = array();
    /**
     * @var array
     */
    private $quotes = array();

    /**
     * @param string|null $url Optional URL to use for this Curl session
     */
    public function __construct($url = null)
    {
        $this->curl_handler = curl_init($url);
    }

    /**
     * Initiate a Curl object.
     *
     * @param string $url Optional URL to use for this Curl session
     *
     * @return Curl The initiated Curl object
     */
    public static function init($url = null)
    {
        return new self($url);
    }

    /**
     * Get information regarding a specific transfer.
     *
     * @param int $opt CURLINFO_* constant
     *
     * @return string|bool|array
     * @see http://www.php.net/manual/en/function.curl-getinfo.php
     */
    public function getInfo($opt = null)
    {
        return curl_getinfo($this->curl_handler, $opt);
    }

    /**
     * Perform a cURL session.
     *
     * @return mixed
     */
    public function execute()
    {
        return curl_exec($this->curl_handler);
    }

    /**
     * Sets an option on the current Curl handler.
     *
     * @param int $option The Curl constant
     * @param mixed $value The value for this option
     *
     * @return Curl
     */
    public function setOption($option, $value)
    {
        curl_setopt($this->curl_handler, $option, $value);

        return $this;
    }

    /**
     * Copy this cURL handle along with all of its preferences.
     *
     * @return resource
     */
    public function copyHandle()
    {
        return curl_copy_handle($this->curl_handler);
    }

    /**
     * Close this cURL session.
     *
     * @return Curl
     */
    public function close()
    {
        curl_close($this->curl_handler);

        return $this;
    }

    /**
     * Get version, error or error_no.
     *
     * @param string $name Property name
     *
     * @return array|int|string
     * @throws \Exception If property is unknown
     */
    public function __get($name)
    {
        switch ($name) {
            case 'error_no':
                return curl_errno($this->curl_handler);
            case 'error':
                return curl_error($this->curl_handler);
            case 'version':
                return curl_version();
            default:
                throw new \Exception(sprintf('Unknown property "%s"', $name));
        }
    }

    /**
     * Get last effective URL.
     *
     * @return string
     */
    public function getEffectiveUrl()
    {
        return $this->getInfo(CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Last received HTTP code.
     *
     * @return string
     */
    public function getHTTPCode()
    {
        return $this->getInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * Remote time of the retrieved document, if -1 is returned the time of the document is unknown.
     *
     * @return string
     */
    public function getFileTime()
    {
        return $this->getInfo(CURLINFO_FILETIME);
    }

    /**
     * Total transaction time in seconds for last transfer.
     *
     * @return string
     */
    public function getTotalTime()
    {
        return $this->getInfo(CURLINFO_TOTAL_TIME);
    }

    /**
     * Time in seconds until name resolving was complete.
     *
     * @return string
     */
    public function getNameLookupTime()
    {
        return $this->getInfo(CURLINFO_NAMELOOKUP_TIME);
    }

    /**
     * Time in seconds it took to establish the connection.
     *
     * @return string
     */
    public function getConnectTime()
    {
        return $this->getInfo(CURLINFO_CONNECT_TIME);
    }

    /**
     * Time in seconds from start until just before file transfer begins.
     *
     * @return string
     */
    public function getPreTransferTime()
    {
        return $this->getInfo(CURLINFO_PRETRANSFER_TIME);
    }

    /**
     * Time in seconds until the first byte is about to be transferred.
     *
     * @return string
     */
    public function getStartTransferTime()
    {
        return $this->getInfo(CURLINFO_STARTTRANSFER_TIME);
    }

    /**
     * Time in seconds of all redirection steps before final transaction was started.
     *
     * @return string
     */
    public function getRedirectTime()
    {
        return $this->getInfo(CURLINFO_REDIRECT_TIME);
    }

    /**
     * Total number of bytes uploaded.
     *
     * @return string
     */
    public function getSizeUpload()
    {
        return $this->getInfo(CURLINFO_SIZE_UPLOAD);
    }

    /**
     * Total number of bytes downloaded.
     *
     * @return string
     */
    public function getSizeDownload()
    {
        return $this->getInfo(CURLINFO_SIZE_DOWNLOAD);
    }

    /**
     * Average download speed.
     *
     * @return string
     */
    public function getSpeedDownload()
    {
        return $this->getInfo(CURLINFO_SPEED_DOWNLOAD);
    }

    /**
     * Average upload speed.
     *
     * @return string
     */
    public function getSpeedUpload()
    {
        return $this->getInfo(CURLINFO_SPEED_UPLOAD);
    }

    /**
     * Total size of all headers received.
     *
     * @return string
     */
    public function getHeaderSize()
    {
        return $this->getInfo(CURLINFO_HEADER_SIZE);
    }

    /**
     * The request string sent.
     * For this to work, add the CURLINFO_HEADER_OUT option to the handle by calling curl_setopt().
     *
     * @return string
     */
    public function getHeaderOut()
    {
        return $this->getInfo(CURLINFO_HEADER_OUT);
    }

    /**
     * Total size of issued requests, currently only for HTTP requests.
     *
     * @return string
     */
    public function getRequestSize()
    {
        return $this->getInfo(CURLINFO_REQUEST_SIZE);
    }

    /**
     * Result of SSL certification verification requested by setting CURLOPT_SSL_VERIFYPEER.
     *
     * @return string
     */
    public function getSSLVerifyResult()
    {
        return $this->getInfo(CURLINFO_SSL_VERIFYRESULT);
    }

    /**
     * Content-Length of download, read from Content-Length: field.
     *
     * @return string
     */
    public function getContentLengthDownload()
    {
        return $this->getInfo(CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }

    /**
     * Specified size of upload.
     *
     * @return string
     */
    public function getContentLengthUpload()
    {
        return $this->getInfo(CURLINFO_CONTENT_LENGTH_UPLOAD);
    }

    /**
     * Content-Type: of the requested document, NULL indicates server did not send valid Content-Type: header.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->getInfo(CURLINFO_CONTENT_TYPE);
    }

    /**
     * TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setAutoReferer($value)
    {
        return $this->setOption(CURLOPT_AUTOREFERER, !!$value);
    }

    /**
     * TRUE to return the raw output when CURLOPT_RETURNTRANSFER is used.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setBinaryTransfer($value)
    {
        return $this->setOption(CURLOPT_BINARYTRANSFER, !!$value);
    }

    /**
     * TRUE to mark this as a new cookie "session".
     * It will force libcurl to ignore all cookies it is about to load that are "session cookies" from
     * the previous session. By default, libcurl always stores and loads all cookies,
     * independent if they are session cookies or not.
     * Session cookies are cookies without expiry date and they are meant to
     * be alive and existing for this "session" only.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setCookieSession($value)
    {
        return $this->setOption(CURLOPT_COOKIESESSION, !!$value);
    }

    /**
     * TRUE to output SSL certification information to STDERR on secure transfers.
     * Available since PHP 5.3.2.
     * Requires CURLOPT_VERBOSE to be on to have an effect.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setCertInfo($value)
    {
        // NOTE: Should this automatically set CURLOPT_VERBOSE?
        // NOTE: This is only available since 5.3.2, so maybe we should
        // check PHP version or the existence of the constant before setting
        return $this->setOption(CURLOPT_CERTINFO, !!$value);
    }

    /**
     * TRUE to convert Unix newlines to CRLF newlines on transfers.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setCrLf($value)
    {
        return $this->setOption(CURLOPT_CRLF, !!$value);
    }

    /**
     * TRUE to use a global DNS cache.
     * This option is not thread-safe and is enabled by default.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setDNSUseGlobalCache($value)
    {
        return $this->setOption(CURLOPT_DNS_USE_GLOBAL_CACHE, !!$value);
    }

    /**
     * TRUE to fail silently if the HTTP code returned is greater than or equal to 400.
     * The default behavior is to return the page normally, ignoring the code.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFailOnError($value)
    {
        return $this->setOption(CURLOPT_FAILONERROR, !!$value);
    }

    /**
     * TRUE to attempt to retrieve the modification date of the remote document.
     * This value can be retrieved using the CURLINFO_FILETIME option with curl_getinfo().
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFileTime($value)
    {
        return $this->setOption(CURLOPT_FILETIME, !!$value);
    }

    /**
     * TRUE to follow any "Location: " header that the server sends as part of the HTTP header
     * (note this is recursive, PHP will follow as many "Location: " headers that it is sent,
     * unless CURLOPT_MAXREDIRS is set).
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFollowLocation($value)
    {
        return $this->setOption(CURLOPT_FOLLOWLOCATION, !!$value);
    }

    /**
     * TRUE to force the connection to explicitly close when it has finished processing, and not be pooled for reuse.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setForbidReuse($value)
    {
        return $this->setOption(CURLOPT_FORBID_REUSE, !!$value);
    }

    /**
     * TRUE to force the use of a new connection instead of a cached one.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFreshConnect($value)
    {
        return $this->setOption(CURLOPT_FRESH_CONNECT, !!$value);
    }

    /**
     * TRUE to use EPRT (and LPRT) when doing active FTP downloads.
     * Use FALSE to disable EPRT and LPRT and use PORT only.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFtpUseEPRT($value)
    {
        return $this->setOption(CURLOPT_FTP_USE_EPRT, !!$value);
    }

    /**
     * TRUE to first try an EPSV command for FTP transfers before reverting back to PASV. Set to FALSE to disable EPSV.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFtpUseEPSV($value)
    {
        return $this->setOption(CURLOPT_FTP_USE_EPSV, !!$value);
    }

    /**
     * TRUE to append to the remote file instead of overwriting it.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFtpAppend($value)
    {
        return $this->setOption(CURLOPT_FTPAPPEND, !!$value);
    }

    /**
     * An alias of CURLOPT_TRANSFERTEXT. Use that instead.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFtpAscii($value)
    {
        return $this->setOption(CURLOPT_FTPASCII, !!$value);
    }

    /**
     * TRUE to only list the names of an FTP directory.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setFtpListOnly($value)
    {
        return $this->setOption(CURLOPT_FTPLISTONLY, !!$value);
    }

    /**
     * TRUE to include the header in the output.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setHeader($value)
    {
        return $this->setOption(CURLOPT_HEADER, !!$value);
    }

    /**
     * TRUE to track the handle's request string.
     * Available since PHP 5.1.3. The CURLINFO_ prefix is intentional.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setHeaderOut($value)
    {
        return $this->setOption(CURLINFO_HEADER_OUT, !!$value);
    }

    /**
     * TRUE to reset the HTTP request method to GET.
     * Since GET is the default, this is only necessary if the request method has been changed.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setHttpGet($value)
    {
        return $this->setOption(CURLOPT_HTTPGET, !!$value);
    }

    /**
     * TRUE to tunnel through a given HTTP proxy.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setHttpProxyTunnel($value)
    {
        return $this->setOption(CURLOPT_HTTPPROXYTUNNEL, !!$value);
    }

    /**
     * TRUE to be completely silent with regards to the cURL functions.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setMute($value)
    {
        return $this->setOption(CURLOPT_MUTE, !!$value);
    }

    /**
     * TRUE to scan the ~/.netrc file to find a username and password for the
     * remote site that a connection is being established with.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setNetRc($value)
    {
        // TODO: Not obvious? -- should probably be renamed to setScanRCFile or setScanRC or setUseRc?
        return $this->setOption(CURLOPT_NETRC, !!$value);
    }

    /**
     * TRUE to exclude the body from the output. Request method is then set to HEAD.
     * Changing this to FALSE does not change it to GET.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setNobody($value)
    {
        return $this->setOption(CURLOPT_NOBODY, !!$value);
    }

    /**
     * TRUE to disable the progress meter for cURL transfers.
     * Note: PHP automatically sets this option to TRUE, this should only be
     * changed for debugging purposes.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setNoProgress($value)
    {
        return $this->setOption(CURLOPT_NOPROGRESS, !!$value);
    }

    /**
     * TRUE to ignore any cURL function that causes a signal to be sent to the PHP process.
     * This is turned on by default in multi-threaded SAPIs so timeout options can still be used.
     * Added in cURL 7.10.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setNoSignal($value)
    {
        return $this->setOption(CURLOPT_NOSIGNAL, !!$value);
    }

    /**
     * TRUE to do a regular HTTP POST. This POST is the normal
     * application/x-www-form-urlencoded kind, most commonly used by HTML forms.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setPost($value)
    {
        return $this->setOption(CURLOPT_POST, !!$value);
    }

    /**
     * TRUE to HTTP PUT a file. The file to PUT must be set with CURLOPT_INFILE and CURLOPT_INFILESIZE.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setPut($value)
    {
        return $this->setOption(CURLOPT_PUT, !!$value);
    }

    /**
     * TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setReturnTransfer($value)
    {
        return $this->setOption(CURLOPT_RETURNTRANSFER, !!$value);
    }

    /**
     * FALSE to stop cURL from verifying the peer's certificate.
     * Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or
     * a certificate directory can be specified with the CURLOPT_CAPATH option.
     * TRUE by default as of cURL 7.10.
     * Default bundle installed as of cURL 7.10.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setSslVerifyPeer($value)
    {
        return $this->setOption(CURLOPT_SSL_VERIFYPEER, !!$value);
    }

    /**
     * TRUE to use ASCII mode for FTP transfers. For LDAP, it retrieves data in plain text instead of HTML.
     * On Windows systems, it will not set STDOUT to binary mode.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setTransferText($value)
    {
        return $this->setOption(CURLOPT_TRANSFERTEXT, !!$value);
    }

    /**
     * TRUE to keep sending the username and password when following locations
     * (using CURLOPT_FOLLOWLOCATION), even when the hostname has changed.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setUnrestrictedAuth($value)
    {
        return $this->setOption(CURLOPT_UNRESTRICTED_AUTH, !!$value);
    }

    /**
     * TRUE to prepare for an upload.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setUpload($value)
    {
        return $this->setOption(CURLOPT_UPLOAD, !!$value);
    }

    /**
     * TRUE to output verbose information. Writes output to STDERR, or the file specified using CURLOPT_STDERR.
     *
     * @param boolean $value
     *
     * @return Curl
     */
    public function setVerbose($value)
    {
        return $this->setOption(CURLOPT_VERBOSE, !!$value);
    }

    /**
     * The size of the buffer to use for each read. There is no guarantee this request will be fulfilled, however.
     * Added in cURL 7.10.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setBufferSize($value)
    {
        return $this->setOption(CURLOPT_BUFFERSIZE, $value);
    }

    /**
     * Either CURLCLOSEPOLICY_LEAST_RECENTLY_USED or CURLCLOSEPOLICY_OLDEST.
     * There are three other CURLCLOSEPOLICY_ constants, but cURL does not support them yet.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setClosePolicy($value)
    {
        return $this->setOption(CURLOPT_CLOSEPOLICY, $value);
    }

    /**
     * The number of seconds to wait while trying to connect.
     * Use 0 to wait indefinitely.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setConnectTimeout($value)
    {
        return $this->setOption(CURLOPT_CONNECTTIMEOUT, $value);
    }

    /**
     * The number of milliseconds to wait while trying to connect.
     * Use 0 to wait indefinitely. If libcurl is built to use the standard system name resolver,
     * that portion of the connect will still use full-second resolution for timeouts
     * with a minimum timeout allowed of one second.
     * Added in cURL 7.16.2.
     * Available since PHP 5.2.3.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setConnectionTimeoutMs($value)
    {
        return $this->setOption(CURLOPT_CONNECTTIMEOUT_MS, $value);
    }

    /**
     * The number of seconds to keep DNS entries in memory.
     * This option is set to 120 (2 minutes) by default.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setDNSCacheTimouet($value)
    {
        return $this->setOption(CURLOPT_DNS_CACHE_TIMEOUT, $value);
    }

    /**
     * The FTP authentication method (when is activated):
     * CURLFTPAUTH_SSL (try SSL first),
     * CURLFTPAUTH_TLS (try TLS first), or
     * CURLFTPAUTH_DEFAULT (let cURL decide).
     * Added in cURL 7.12.2.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setFtpSslAuth($value)
    {
        return $this->setOption(CURLOPT_FTPSSLAUTH, $value);
    }

    /**
     * CURL_HTTP_VERSION_NONE (default, lets Curl decide which version to use),
     * CURL_HTTP_VERSION_1_0 (forces HTTP/1.0), or
     * CURL_HTTP_VERSION_1_1 (forces HTTP/1.1).
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setHttpVersion($value)
    {
        return $this->setOption(CURLOPT_HTTP_VERSION, $value);
    }

    /**
     * The HTTP authentication method(s) to use. The options are:
     * CURLAUTH_BASIC,
     * CURLAUTH_DIGEST,
     * CURLAUTH_GSSNEGOTIATE,
     * CURLAUTH_NTLM,
     * CURLAUTH_ANY, and
     * CURLAUTH_ANYSAFE.
     * The bitwise | (or) operator can be used to combine more than one method.
     * If this is done, cURL will poll the server to see what methods it supports and pick the best one.
     *
     * CURLAUTH_ANY is an alias for
     * CURLAUTH_BASIC | CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
     *
     * CURLAUTH_ANYSAFE is an alias for
     * CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setHttpAuth($value)
    {
        return $this->setOption(CURLOPT_HTTPAUTH, $value);
    }

    /**
     * The expected size, in bytes, of the file when uploading a file to a remote site.
     * Note that using this option will not stop libcurl from sending more data,
     * as exactly what is sent depends on CURLOPT_READFUNCTION.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setInFileSize($value)
    {
        return $this->setOption(CURLOPT_INFILESIZE, $value);
    }

    /**
     * The transfer speed, in bytes per second, that the transfer should be
     * below during the count of CURLOPT_LOW_SPEED_TIME seconds before PHP considers the transfer too slow and aborts.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setLowSpeedLimit($value)
    {
        return $this->setOption(CURLOPT_LOW_SPEED_LIMIT, $value);
    }

    /**
     * The number of seconds the transfer speed should be below CURLOPT_LOW_SPEED_LIMIT
     * before PHP considers the transfer too slow and aborts.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setLowSpeedTime($value)
    {
        return $this->setOption(CURLOPT_LOW_SPEED_TIME, $value);
    }

    /**
     * The maximum amount of persistent connections that are allowed.
     * When the limit is reached, CURLOPT_CLOSEPOLICY is used to determine which connection to close.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setMaxConnects($value)
    {
        return $this->setOption(CURLOPT_MAXCONNECTS, $value);
    }

    /**
     * The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setMaxRedirs($value)
    {
        return $this->setOption(CURLOPT_MAXREDIRS, $value);
    }

    /**
     * An alternative port number to connect to.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setPort($value)
    {
        return $this->setOption(CURLOPT_PORT, $value);
    }

    /**
     * Bitmask of CURLPROTO_* values. If used, this bitmask limits what protocols libcurl may use in the transfer.
     * This allows you to have a libcurl built to support a wide range of protocols but still limit specific
     * transfers to only be allowed to use a subset of them. By default libcurl will accept all protocols it supports.
     * See also CURLOPT_REDIR_PROTOCOLS.
     *
     * Valid protocol options are:
     * CURLPROTO_HTTP,
     * CURLPROTO_HTTPS,
     * CURLPROTO_FTP,
     * CURLPROTO_FTPS,
     * CURLPROTO_SCP,
     * CURLPROTO_SFTP,
     * CURLPROTO_TELNET,
     * CURLPROTO_LDAP,
     * CURLPROTO_LDAPS,
     * CURLPROTO_DICT,
     * CURLPROTO_FILE,
     * CURLPROTO_TFTP,
     * CURLPROTO_ALL
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setProtocols($value)
    {
        return $this->setOption(CURLOPT_PROTOCOLS, $value);
    }

    /**
     * The HTTP authentication method(s) to use for the proxy connection.
     * Use the same bitmasks as described in CURLOPT_HTTPAUTH.
     * For proxy authentication, only CURLAUTH_BASIC and CURLAUTH_NTLM are currently supported.
     * Added in cURL 7.10.7.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setProxyAuthr($value)
    {
        return $this->setOption(CURLOPT_PROXYAUTH, $value);
    }

    /**
     * The port number of the proxy to connect to. This port number can also be set in CURLOPT_PROXY.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setProxyPort($value)
    {
        return $this->setOption(CURLOPT_PROXYPORT, $value);
    }

    /**
     * Either CURLPROXY_HTTP (default) or CURLPROXY_SOCKS5.
     * Added in cURL 7.10.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setProxyType($value)
    {
        return $this->setOption(CURLOPT_PROXYTYPE, $value);
    }

    /**
     * Bitmask of CURLPROTO_* values.
     * If used, this bitmask limits what protocols libcurl may use in a transfer that it follows to in a redirect
     * when CURLOPT_FOLLOWLOCATION is enabled. This allows you to limit specific transfers to only be allowed to use
     * a subset of protocols in redirections. By default libcurl will allow all protocols except for FILE and SCP.
     * This is a difference compared to pre-7.19.4 versions which
     * unconditionally would follow to all protocols supported.
     * Added in cURL 7.19.4.
     *
     * @see CURLOPT_PROTOCOLS for protocol constant values.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setRedirProtocols($value)
    {
        return $this->setOption(CURLOPT_REDIR_PROTOCOLS, $value);
    }

    /**
     * The offset, in bytes, to resume a transfer from.
     *
     * @param int $value
     *
     * @return Curl
     *
     */
    public function setResumeFrom($value)
    {
        return $this->setOption(CURLOPT_RESUME_FROM, $value);
    }

    /**
     * 1 to check the existence of a common name in the SSL peer certificate.
     * 2 to check the existence of a common name and also verify that it matches the hostname provided.
     * In production environments the value of this option should be kept at 2 (default value).
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setSSLVerifyHost($value)
    {
        return $this->setOption(CURLOPT_SSL_VERIFYHOST, $value);
    }

    /**
     * The SSL version (2 or 3) to use. By default PHP will try to determine this itself,
     * although in some cases this must be set manually.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setSSLVersion($value)
    {
        return $this->setOption(CURLOPT_SSLVERSION, $value);
    }

    /**
     * How CURLOPT_TIMEVALUE is treated. Use CURL_TIMECOND_IFMODSINCE to return
     * the page only if it has been modified since the time specified in CURLOPT_TIMEVALUE.
     * If it hasn't been modified, a "304 Not Modified" header will be returned assuming CURLOPT_HEADER is TRUE.
     * Use CURL_TIMECOND_IFUNMODSINCE for the reverse effect.
     * CURL_TIMECOND_IFMODSINCE is the default.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setTimeCondition($value)
    {
        return $this->setOption(CURLOPT_TIMECONDITION, $value);
    }

    /**
     * The maximum number of seconds to allow cURL functions to execute.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setTimeOut($value)
    {
        return $this->setOption(CURLOPT_TIMEOUT, $value);
    }

    /**
     * The maximum number of milliseconds to allow cURL functions to execute.
     * If libcurl is built to use the standard system name resolver, that portion of the connect will still use
     * full-second resolution for timeouts with a minimum timeout allowed of one second.
     * Added in cURL 7.16.2.
     * Available since PHP 5.2.3.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setTimeOutMs($value)
    {
        return $this->setOption(CURLOPT_TIMEOUT_MS, $value);
    }

    /**
     * The time in seconds since January 1st, 1970. The time will be used by CURLOPT_TIMECONDITION.
     * By default, CURL_TIMECOND_IFMODSINCE is used.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setTimeValue($value)
    {
        return $this->setOption(CURLOPT_TIMEVALUE, $value);
    }

    /**
     * If a download exceeds this speed (counted in bytes per second) on cumulative average during the transfer,
     * the transfer will pause to keep the average rate less than or equal to the parameter value.
     * Defaults to unlimited speed.
     * Added in cURL 7.15.5.
     * Available since PHP 5.4.0.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setMaxRecvSpeedLarge($value)
    {
        // TODO: check for this before setting, since it requires a later PHP version
        return $this->setOption(CURLOPT_MAX_RECV_SPEED_LARGE, $value);
    }

    /**
     * If an upload exceeds this speed (counted in bytes per second) on cumulative average during the transfer,
     * the transfer will pause to keep the average rate less than or equal to the parameter value.
     * Defaults to unlimited speed.
     * Added in cURL 7.15.5.
     * Available since PHP 5.4.0.
     *
     * @param int $value
     *
     * @return Curl
     */
    public function setMaxSendSpeedLarge($value)
    {
        // TODO: check for this before setting, since it requires a later PHP version
        return $this->setOption(CURLOPT_MAX_SEND_SPEED_LARGE, $value);
    }

    /**
     * The name of a file holding one or more certificates to verify the peer with.
     * This only makes sense when used in combination with CURLOPT_SSL_VERIFYPEER.
     * Requires absolute path.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setCAInfo($value)
    {
        return $this->setOption(CURLOPT_CAINFO, $value);
    }

    /**
     * A directory that holds multiple CA certificates. Use this option alongside CURLOPT_SSL_VERIFYPEER.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setCAPath($value)
    {
        return $this->setOption(CURLOPT_CAPATH, $value);
    }

    /**
     * The contents of the "Cookie: " header to be used in the HTTP request.
     * Note that multiple cookies are separated with a semicolon followed by a space (e.g., "fruit=apple; colour=red")
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setCookie($value)
    {
        return $this->setOption(CURLOPT_COOKIE, $value);
    }

    /**
     * The name of the file containing the cookie data. The cookie file can be in Netscape format,
     * or just plain HTTP-style headers dumped into a file.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setCookieFile($value)
    {
        return $this->setOption(CURLOPT_COOKIEFILE, $value);
    }

    /**
     * The name of a file to save all internal cookies to when the handle is closed, e.g. after a call to curl_close.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setCookieJar($value)
    {
        return $this->setOption(CURLOPT_COOKIEJAR, $value);
    }

    /**
     * A custom request method to use instead of "GET" or "HEAD" when doing a HTTP request.
     * This is useful for doing "DELETE" or other, more obscure HTTP requests.
     * Valid values are things like "GET", "POST", "CONNECT" and so on;
     * i.e. Do not enter a whole HTTP request line here. For instance,
     * entering "GET /index.html HTTP/1.0\r\n\r\n" would be incorrect.
     *
     * Note:
     * Don't do this without making sure the server supports the custom request method first.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setCustomRequest($value)
    {
        return $this->setOption(CURLOPT_CUSTOMREQUEST, $value);
    }

    /**
     * Like CURLOPT_RANDOM_FILE, except a filename to an Entropy Gathering Daemon socket.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setEGDSocket($value)
    {
        return $this->setOption(CURLOPT_EGDSOCKET, $value);
    }

    /**
     * The contents of the "Accept-Encoding: " header. This enables decoding of the response.
     * Supported encodings are "identity", "deflate", and "gzip".
     * If an empty string, "", is set, a header containing all supported encoding types is sent.
     * Added in cURL 7.10.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setEncoding($value)
    {
        return $this->setOption(CURLOPT_ENCODING, $value);
    }

    /**
     * The value which will be used to get the IP address to use for the FTP "POST" instruction.
     * The "POST" instruction tells the remote server to connect to our specified IP address.
     * The string may be a plain IP address, a hostname, a network interface name (under Unix),
     * or just a plain '-' to use the systems default IP address.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setFtpPort($value)
    {
        return $this->setOption(CURLOPT_FTPPORT, $value);
    }

    /**
     * The name of the outgoing network interface to use. This can be an interface name, an IP address or a host name.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setInterface($value)
    {
        return $this->setOption(CURLOPT_INTERFACE, $value);
    }

    /**
     * The KRB4 (Kerberos 4) security level.
     * Any of the following values (in order from least to most powerful) are valid:
     * "clear", "safe", "confidential", "private"..
     * If the string does not match one of these, "private" is used.
     * Setting this option to NULL will disable KRB4 security. Currently KRB4 security only works with FTP transactions.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setKRB4Level($value)
    {
        return $this->setOption(CURLOPT_KRB4LEVEL, $value);
    }

    /**
     * The full data to post in a HTTP "POST" operation.
     * To post a file, prepend a filename with @ and use the full path.
     * The filetype can be explicitly specified by following the filename with the type in the format ';type=mimetype'.
     * This parameter can either be passed as a urlencoded string like 'para1=val1&para2=val2&...' or as an array with
     * the field name as key and field data as value.
     * If value is an array, the Content-Type header will be set to multipart/form-data.
     * As of PHP 5.2.0, files thats passed to this option with the @ prefix must be in array form to work.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setPostFields($value)
    {
        return $this->setOption(CURLOPT_POSTFIELDS, $value);
    }

    /**
     * The HTTP proxy to tunnel requests through.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setProxy($value)
    {
        return $this->setOption(CURLOPT_PROXY, $value);
    }

    /**
     * A username and password formatted as "[username]:[password]" to use for the connection to the proxy.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setProxyUserPwd($value)
    {
        return $this->setOption(CURLOPT_PROXYUSERPWD, $value);
    }

    /**
     * A filename to be used to seed the random number generator for SSL.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setRandomFile($value)
    {
        return $this->setOption(CURLOPT_RANDOM_FILE, $value);
    }

    /**
     * Range(s) of data to retrieve in the format "X-Y" where X or Y are optional.
     * HTTP transfers also support several intervals, separated with commas in the format "X-Y,N-M".
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setRange($value)
    {
        return $this->setOption(CURLOPT_RANGE, $value);
    }

    /**
     * The contents of the "Referer: " header to be used in a HTTP request.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setReferer($value)
    {
        return $this->setOption(CURLOPT_REFERER, $value);
    }

    /**
     * A list of ciphers to use for SSL. For example, RC4-SHA and TLSv1 are valid cipher lists.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLCipherList($value)
    {
        return $this->setOption(CURLOPT_SSL_CIPHER_LIST, $value);
    }

    /**
     * The name of a file containing a PEM formatted certificate.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLCert($value)
    {
        return $this->setOption(CURLOPT_SSLCERT, $value);
    }

    /**
     * The password required to use the CURLOPT_SSLCERT certificate.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLCertPasswd($value)
    {
        return $this->setOption(CURLOPT_SSLCERTPASSWD, $value);
    }

    /**
     * The format of the certificate. Supported formats are "PEM" (default), "DER", and "ENG".
     * Added in cURL 7.9.3.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLCertType($value)
    {
        return $this->setOption(CURLOPT_SSLCERTTYPE, $value);
    }

    /**
     * The identifier for the crypto engine of the private SSL key specified in CURLOPT_SSLKEY.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLEngine($value)
    {
        return $this->setOption(CURLOPT_SSLENGINE, $value);
    }

    /**
     * The identifier for the crypto engine used for asymmetric crypto operations.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLEngineDefailt($value)
    {
        return $this->setOption(CURLOPT_SSLENGINE_DEFAULT, $value);
    }

    /**
     * The name of a file containing a private SSL key.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLKey($value)
    {
        return $this->setOption(CURLOPT_SSLKEY, $value);
    }

    /**
     * The secret password needed to use the private SSL key specified in CURLOPT_SSLKEY.
     * Note:
     * Since this option contains a sensitive password, remember to keep the
     * PHP script it is contained within safe.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLKeyPasswd($value)
    {
        return $this->setOption(CURLOPT_SSLKEYPASSWD, $value);
    }

    /**
     * The key type of the private SSL key specified in CURLOPT_SSLKEY.
     * Supported key types are "PEM" (default), "DER", and "ENG".
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setSSLKeyType($value)
    {
        return $this->setOption(CURLOPT_SSLKEYTYPE, $value);
    }

    /**
     * The URL to fetch. This can also be set when initializing a session with curl_init().
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setURL($value)
    {
        return $this->setOption(CURLOPT_URL, $value);
    }

    /**
     * The contents of the "User-Agent: " header to be used in a HTTP request.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setUserAgent($value)
    {
        return $this->setOption(CURLOPT_USERAGENT, $value);
    }

    /**
     * A username and password formatted as "[username]:[password]" to use for the connection.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setUserPwd($value)
    {
        return $this->setOption(CURLOPT_USERPWD, $value);
    }

    /**
     * Add a single HTTP 200 alias.
     * HTTP 200 response that will be treated as valid responses and not as errors.
     *
     * @param string $alias
     *
     * @return Curl
     */
    public function addHttp200Alias($alias)
    {
        $this->http200Aliases[] = $alias;

        return $this->setHttp200Aliases($this->http200Aliases);
    }

    /**
     * An array of HTTP 200 responses that will be treated as valid responses and not as errors.
     * Added in cURL 7.10.3.
     *
     * @param array $value
     *
     * @return Curl
     */
    public function setHttp200Aliases(array $value = array())
    {
        $this->http200Aliases = array_values($value);

        return $this->setOption(CURLOPT_HTTP200ALIASES, $this->http200Aliases);
    }

    /**
     * Add a single HTTP header
     *
     * @param string $key The HTTP header key
     * @param string $value The HTTP value value
     *
     * @return Curl
     */
    public function addHttpHeader($key, $value)
    {
        $this->httpHeaders[] = sprintf("%s: %s", $key, $value);

        return $this->setHttpHeader($this->httpHeaders);
    }

    /**
     * An array of HTTP header fields to set, in the format
     * array('Content-type: text/plain', 'Content-length: 100')
     *
     * @param array $value
     *
     * @return Curl
     */
    public function setHttpHeader(array $value)
    {
        $this->httpHeaders = $value;

        return $this->setOption(CURLOPT_HTTPHEADER, $this->httpHeaders);
    }

    /**
     * Add a single FTP command to execute on the server after the FTP request has been performed.
     *
     * @param string $command
     *
     * @return Curl
     */
    public function addPostQuote($command)
    {
        $this->postQuotes[] = $command;

        return $this->setPostQuote($this->postQuotes);
    }

    /**
     * An array of FTP commands to execute on the server after the FTP request has been performed.
     *
     * @param array $value
     *
     * @return Curl
     */
    public function setPostQuote(array $value)
    {
        $this->postQuotes = $value;

        return $this->setOption(CURLOPT_POSTQUOTE, $value);
    }

    /**
     * Add a single FTP commands to execute on the server prior to the FTP request.
     *
     * @param string $command The command to add
     *
     * @return Curl
     */
    public function addQuote($command)
    {
        $this->quotes[] = $command;

        return $this->setQuote($this->quotes);
    }

    /**
     * An array of FTP commands to execute on the server prior to the FTP request.
     *
     * @param array $value
     *
     * @return Curl
     */
    public function setQuote(array $value)
    {
        $this->quotes = $value;

        return $this->setOption(CURLOPT_QUOTE, $value);
    }

    /**
     * The file that the transfer should be written to.
     * The default is STDOUT (the browser window).
     *
     * @param resource $value
     *
     * @return Curl
     */
    public function setFile($value)
    {
        return $this->setOption(CURLOPT_FILE, $value);
    }

    /**
     * The file that the transfer should be read from when uploading.
     *
     * @param resource $value
     *
     * @return Curl
     */
    public function setInFile($value)
    {
        return $this->setOption(CURLOPT_INFILE, $value);
    }

    /**
     * An alternative location to output errors to instead of STDERR.
     *
     * @param resource $value
     *
     * @return Curl
     */
    public function setStdErr($value)
    {
        return $this->setOption(CURLOPT_STDERR, $value);
    }

    /**
     * The file that the header part of the transfer is written to.
     *
     * @param resource $value
     *
     * @return Curl
     */
    public function setWriteHeader($value)
    {
        return $this->setOption(CURLOPT_WRITEHEADER, $value);
    }

    /**
     * The name of a callback function where the callback function takes two parameters.
     * The first is the cURL resource, the second is a string with the header data to be written.
     * The header data must be written when using this callback function. Return the number of bytes written.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setHeaderFunction($value)
    {
        return $this->setOption(CURLOPT_HEADERFUNCTION, $value);
    }

    /**
     * The name of a callback function where the callback function takes three parameters.
     * The first is the cURL resource, the second is a string containing a password prompt,
     * and the third is the maximum password length. Return the string containing the password.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setPasswdFunction($value)
    {
        return $this->setOption(CURLOPT_PASSWDFUNCTION, $value);
    }

    /**
     * The name of a callback function where the callback function takes three parameters.
     * The first is the cURL resource, the second is a file-descriptor resource, and the third is length.
     * Return the string containing the data.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setProgressFunction($value)
    {
        return $this->setOption(CURLOPT_PROGRESSFUNCTION, $value);
    }

    /**
     * The name of a callback function where the callback function takes three parameters.
     * The first is the cURL resource, the second is a stream resource provided to cURL
     * through the option CURLOPT_INFILE, and the third is the maximum amount of data to be read.
     * The callback function must return a string with a length equal or smaller than the amount of data requested,
     * typically by reading it from the passed stream resource.
     * It should return an empty string to signal EOF.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setReadFunction($value)
    {
        return $this->setOption(CURLOPT_READFUNCTION, $value);
    }

    /**
     * The name of a callback function where the callback function takes two parameters.
     * The first is the cURL resource, and the second is a string with the data to be written.
     * The data must be saved by using this callback function.
     * It must return the exact number of bytes written or the transfer will be aborted with an error.
     *
     * @param string $value
     *
     * @return Curl
     */
    public function setWriteFunction($value)
    {
        return $this->setOption(CURLOPT_WRITEFUNCTION, $value);
    }
}
