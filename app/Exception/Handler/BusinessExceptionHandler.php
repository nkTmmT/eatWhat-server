<?php
/**
 * User: TmmT
 * Date: 2020/7/24
 * Time: 16:37
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Exception\Handler;


use App\Exception\BusinessException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BusinessExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;
    
    /**
     * @var FormatterInterface
     */
    protected $formatter;
    
    public function __construct(StdoutLoggerInterface $logger, FormatterInterface $formatter)
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
    }
    
    /**
     * @param BusinessException $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->debug($this->formatter->format($throwable));
        $this->stopPropagation();
        $data = Json::encode(['code' => $throwable->getCode(), 'msg' => $throwable->getMessage()]);
        return $response->withStatus(403)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($data));
    }
    
    /**
     * Determine if the current exception handler should handle the exception,.
     *
     * @return bool
     *              If return true, then this exception handler will handle the exception,
     *              If return false, then delegate to next handler
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}