<?php
/*
 * This file is part of the IBBL project.
 *
 * (c) Anis Uddin Ahmad <anisniit@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AppBundle\Traits;


trait Mailer 
{
    private $_dic;
    /**
     * Renders Twig template and send email using Swift_Mailer
     *
     * @param string $subject     Email subject
     * @param string|array $to    Email receipents. Accepts formats accepted by Swift_Message addresses
     * @param string $template    A Twig Template logical path (e,g, BundleName:ViewDir:x.html.twig)
     * @param array $params       Array of params to render template
     * @param string|array $from  Will use application config if not provided
     * @param string $contentType text/html|text/plain
     *
     * @return mixed
     */
    public function mailTwigView($subject, $to, $template, $params = [], $from = null, $contentType = 'text/html')
    {
        $body = $this->dic()->get('templating')->render($template, $params);
        return $this->mail($subject, $to, $body, $from, $contentType);
    }

    /**
     * Send emails using Swift_Mailer
     *
     * @param string $subject     Email subject
     * @param string|array $to    Email receipents. Accepts formats accepted by Swift_Message addresses
     * @param string $body
     * @param string|array $from  Will use application config if not provided
     * @param string $contentType text/html|text/plain
     *
     * @return mixed
     */
    public function mail($subject, $to, $body, $from = null, $contentType = 'text/html')
    {
        $message = \Swift_Message::newInstance()
             ->setSubject($subject)
             ->setFrom(($from ?: $this->mailFrom()))
             ->setTo($to)
             ->setBody($body, $contentType);

        return $this->dic()->get('mailer')->send($message);
    }

    /**
     * Manually flush email queue
     */
    public function flushMailQueue()
    {
        $transport = $this->dic()->get('mailer')->getTransport();
        $spool = $transport->getSpool();

        $spool->flushQueue($this->dic()->get('swiftmailer.transport.real'));
    }

    private function mailFrom()
    {
        return [$this->dic()->getParameter('email.sender.email') => $this->dic()->getParameter('email.sender.name')];
    }

    /**
     * Find the Dipendency Injection Container
     * @return mixed
     */
    private function dic()
    {
        if(empty($this->_dic)){
            if(method_exists($this, 'getContainer')) {
                $this->_dic = $this->getContainer();
            } else if(! empty($this->container)) {
                $this->_dic = $this->container;
            } else {
                throw new \RuntimeException(__CLASS__ .' do not have a container property or getContainer method. So cannot use trait '. __TRAIT__);
            }
        }

        return $this->_dic;
    }

} 