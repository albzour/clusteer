<?php

namespace RenokiCo\Clusteer;

class Clusteer
{
    const DESKTOP_DEVICE = 'desktop';

    const TABLET_DEVICE = 'tablet';

    const MOBILE_DEVICE = 'mobile';

    /**
     * Get the parameters sent to Clusteer.
     *
     * @var array
     */
    protected $query = [];

    /**
     * Get the URL to crawl.
     *
     * @var string
     */
    protected $url;

    /**
     * Initialize a Clusteer instance with an URL.
     *
     * @param  string  $url
     * @return $this
     */
    public static function to(string $url)
    {
        return (new static)->setUrl($url);
    }

    /**
     * Set the URL address.
     *
     * @param  string  $url
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the viewport.
     *
     * @param  int  $width
     * @param  int  $height
     * @return $this
     */
    public function setViewport(int $width, int $height)
    {
        return $this->setParameter('viewport', "{$width}x{$height}");
    }

    /**
     * Set the device.
     *
     * @param  string  $device
     * @return $this
     */
    public function setDevice(string $device)
    {
        return $this->setParameter('device', $device);
    }

    /**
     * Set the user agent. Overwrites the `setDevice` method.
     *
     * @param  string  $userAgent
     * @return $this
     */
    public function setUserAgent(string $userAgent)
    {
        return $this->setParameter('user_agent', $userAgent);
    }

    /**
     * Set the extra headers. They get serialized as JSON.
     *
     * @param  array  $headers
     * @return $this
     */
    public function setExtraHeaders(array $headers)
    {
        return $this->setParameter('extra_headers', json_encode($headers));
    }

    /**
     * Set the extensions to block.
     *
     * @param  array  $extensions
     * @return $this
     */
    public function blockExtensions(array $extensions)
    {
        return $this->setParameter('blocked_extensions', join(',', $extensions));
    }

    /**
     * Set the timeout.
     *
     * @param  int  $seconds
     * @return $this
     */
    public function timeout(int $seconds)
    {
        return $this->setParameter('timeout', $seconds);
    }

    /**
     * Wait until all the requests get triggered.
     *
     * @return $this
     */
    public function waitUntilAllRequestsFinish()
    {
        return $this->setParameter('until_idle', 1);
    }

    /**
     * Output the triggered requests.
     *
     * @return $this
     */
    public function withTriggeredRequests()
    {
        return $this->setParameter('triggered_requests', 1);
    }

    /**
     * Output the cookies.
     *
     * @return $this
     */
    public function withCookies()
    {
        return $this->setParameter('cookies', 1);
    }

    /**
     * Output the HTML.
     *
     * @return $this
     */
    public function withHtml()
    {
        return $this->setParameter('html', 1);
    }

    /**
     * Trigger the crawling.
     *
     * @return ClusteerResponse
     */
    public function get(): ClusteerResponse
    {
        $response = json_decode(
            file_get_contents($this->getCallableUrl()), true
        )['data'];

        return new ClusteerResponse($response);
    }

    /**
     * Set a parameter for Clusteer.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setParameter(string $key, $value)
    {
        $this->query[$key] = $value;

        return $this;
    }

    /**
     * Get the callable URL.
     *
     * @return string
     */
    protected function getCallableUrl(): string
    {
        // Ensure url is at the end of the query string.
        $this->setParameter('url', $this->url);

        $endpoint = config('clusteer.endpoint');
        $query = http_build_query($this->query);

        return "{$endpoint}?{$query}";
    }
}
